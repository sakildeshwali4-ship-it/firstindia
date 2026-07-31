<?php

namespace App\Services;

use App\Models\Reels\CoinPackage;
use App\Models\Reels\Episode;
use App\Models\Reels\UserEpisodePurchase;
use App\Models\Reels\WalletTransaction;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReelsWalletService
{
    public function createPendingRecharge(Users $user, CoinPackage $package, string $paymentId): WalletTransaction
    {
        return WalletTransaction::query()->create([
            'user_id' => $user->id,
            'coin_package_id' => $package->id,
            'payment_id' => $paymentId,
            'transaction_type' => 'credit',
            'source' => 'recharge',
            'status' => 'pending',
            'coins' => (int) $package->coins,
            'balance_before' => (int) ($user->wallet ?? 0),
            'balance_after' => (int) ($user->wallet ?? 0),
            'amount_rupees' => (int) $package->price_rupees,
            'description' => 'Pending wallet recharge for '.$package->name,
            'meta' => [
                'package_name' => $package->name,
            ],
        ]);
    }

    public function creditFromPackage(Users $user, CoinPackage $package, ?string $paymentId = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $package, $paymentId): WalletTransaction {
            $walletUser = Users::query()->lockForUpdate()->findOrFail($user->id);
            $before = (int) ($walletUser->wallet ?? 0);
            $coins = (int) $package->coins;
            $after = $before + $coins;

            $walletUser->wallet = $after;
            $walletUser->save();

            return WalletTransaction::query()->create([
                'user_id' => $walletUser->id,
                'coin_package_id' => $package->id,
                'payment_id' => $paymentId,
                'transaction_type' => 'credit',
                'source' => 'recharge',
                'coins' => $coins,
                'balance_before' => $before,
                'balance_after' => $after,
                'amount_rupees' => (int) $package->price_rupees,
                'description' => 'Wallet recharge for '.$package->name,
                'meta' => [
                    'package_name' => $package->name,
                ],
            ]);
        });
    }

    public function completePendingRecharge(
        Users $user,
        CoinPackage $package,
        WalletTransaction $pendingTransaction,
        string $gatewayOrderId,
        string $gatewayPaymentId,
        string $gatewaySignature,
        array $paymentMeta = []
    ): WalletTransaction {
        return DB::transaction(function () use (
            $user,
            $package,
            $pendingTransaction,
            $gatewayOrderId,
            $gatewayPaymentId,
            $gatewaySignature,
            $paymentMeta
        ): WalletTransaction {
            $walletUser = Users::query()->lockForUpdate()->findOrFail($user->id);
            $transaction = WalletTransaction::query()->lockForUpdate()->findOrFail($pendingTransaction->id);

            if ($transaction->status === 'success') {
                return $transaction;
            }

            $before = (int) ($walletUser->wallet ?? 0);
            $coins = (int) $package->coins;
            $after = $before + $coins;

            $walletUser->wallet = $after;
            $walletUser->save();

            $transaction->update([
                'gateway_order_id' => $gatewayOrderId,
                'gateway_payment_id' => $gatewayPaymentId,
                'gateway_signature' => $gatewaySignature,
                'status' => 'success',
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => 'Wallet recharge for '.$package->name,
                'verified_at' => now(),
                'meta' => array_merge($transaction->meta ?? [], $paymentMeta),
            ]);

            return $transaction->fresh();
        });
    }

    public function unlockEpisode(Users $user, Episode $episode): array
    {
        return DB::transaction(function () use ($user, $episode): array {
            $walletUser = Users::query()->lockForUpdate()->findOrFail($user->id);

            $existingPurchase = UserEpisodePurchase::query()
                ->where('user_id', $walletUser->id)
                ->where('episode_id', $episode->id)
                ->first();

            if ($existingPurchase) {
                return [
                    'already_unlocked' => true,
                    'wallet_balance' => (int) ($walletUser->wallet ?? 0),
                    'purchase' => $existingPurchase,
                ];
            }

            if (! $this->episodeRequiresPurchase($episode)) {
                $purchase = UserEpisodePurchase::query()->create([
                    'user_id' => $walletUser->id,
                    'episode_id' => $episode->id,
                    'coins_spent' => 0,
                    'purchased_at' => now(),
                ]);

                return [
                    'already_unlocked' => false,
                    'wallet_balance' => (int) ($walletUser->wallet ?? 0),
                    'purchase' => $purchase,
                ];
            }

            $before = (int) ($walletUser->wallet ?? 0);
            $cost = (int) $episode->coin_price;

            if ($before < $cost) {
                throw new RuntimeException('Insufficient wallet balance.');
            }

            $after = $before - $cost;
            $walletUser->wallet = $after;
            $walletUser->save();

            $purchase = UserEpisodePurchase::query()->create([
                'user_id' => $walletUser->id,
                'episode_id' => $episode->id,
                'coins_spent' => $cost,
                'purchased_at' => now(),
            ]);

            WalletTransaction::query()->create([
                'user_id' => $walletUser->id,
                'episode_id' => $episode->id,
                'series_id' => $episode->series_id,
                'transaction_type' => 'debit',
                'source' => 'episode_unlock',
                'coins' => $cost,
                'balance_before' => $before,
                'balance_after' => $after,
                'amount_rupees' => null,
                'description' => 'Coins used to unlock episode '.$episode->title,
                'meta' => [
                    'episode_number' => $episode->number,
                ],
            ]);

            return [
                'already_unlocked' => false,
                'wallet_balance' => $after,
                'purchase' => $purchase,
            ];
        });
    }

    public function hasEpisodeAccess(int $userId, Episode $episode): bool
    {
        if (! $this->episodeRequiresPurchase($episode)) {
            return true;
        }

        return UserEpisodePurchase::query()
            ->where('user_id', $userId)
            ->where('episode_id', $episode->id)
            ->exists();
    }

    private function episodeRequiresPurchase(Episode $episode): bool
    {
        return (bool) $episode->is_premium && (int) $episode->coin_price > 0;
    }
}
