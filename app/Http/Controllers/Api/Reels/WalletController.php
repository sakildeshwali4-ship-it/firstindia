<?php

namespace App\Http\Controllers\Api\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\CoinPackage;
use App\Models\Reels\Episode;
use App\Models\Reels\WalletTransaction;
use App\Models\Payment_Option;
use App\Models\Users;
use App\Services\ReelsWalletService;
use Illuminate\Http\Request;
use RuntimeException;
use Validator;

class WalletController extends Controller
{
    public function __construct(private ReelsWalletService $walletService)
    {
    }

    public function packages(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }

        return [
            'currency' => 'INR',
            'wallet_balance' => (int) (Users::query()->whereKey($request->user_id)->value('wallet') ?? 0),
            'data' => CoinPackage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('price_rupees')
                ->get()
                ->map(fn (CoinPackage $package): array => [
                    'id' => $package->id,
                    'name' => $package->name,
                    'coins' => $package->coins,
                    'price_rupees' => $package->price_rupees,
                ])
                ->values()
                ->all(),
        ];
    }

    public function recharge(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
            'package_id' => 'required|exists:coin_packages,id',
            'payment_id' => 'nullable|string|max:190',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }

        $user = Users::query()->findOrFail($request->user_id);
        $package = CoinPackage::query()
            ->where('id', $request->package_id)
            ->where('is_active', true)
            ->first();

        if (! $package) {
            return [
                'status' => 400,
                'message' => 'Coin plan not found or inactive.',
            ];
        }

        $transaction = $this->walletService->creditFromPackage(
            $user,
            $package,
            $request->payment_id
        );

        return [
            'status' => 200,
            'message' => 'Wallet recharged successfully.',
            'wallet' => (int) $transaction->balance_after,
            'data' => [
                'package_id' => $package->id,
                'package_name' => $package->name,
                'price_rupees' => (int) $package->price_rupees,
                'coins' => (int) $package->coins,
                'payment_id' => $transaction->payment_id,
            ],
        ];
    }

    public function createRazorpayOrder(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
            'package_id' => 'required|exists:coin_packages,id',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 400,
                'message' => __('api_msg.please_enter_required_fields'),
            ];
        }

        $credentials = $this->razorpayCredentials();
        if (! $credentials['key'] || ! $credentials['secret']) {
            return [
                'status' => 400,
                'message' => 'Razorpay credentials are not configured.',
            ];
        }

        $user = Users::query()->findOrFail($request->user_id);
        $package = CoinPackage::query()
            ->where('id', $request->package_id)
            ->where('is_active', true)
            ->first();

        if (! $package) {
            return [
                'status' => 400,
                'message' => 'Coin plan not found or inactive.',
            ];
        }

        $localOrderId = $this->generateWalletOrderId($user->id);
        $amountPaise = (int) $package->price_rupees * 100;
        $pendingTransaction = $this->walletService->createPendingRecharge($user, $package, $localOrderId);

        $fields = [
            'amount' => $amountPaise,
            'currency' => 'INR',
            'receipt' => $localOrderId,
            'notes' => [
                'user_id' => (string) $user->id,
                'package_id' => (string) $package->id,
                'local_order_id' => $localOrderId,
                'order_type' => 'coin_package',
            ],
        ];

        $response = $this->callRazorpay('https://api.razorpay.com/v1/orders', $credentials, json_encode($fields), true);
        $decoded = json_decode($response, true);

        if (empty($decoded['id']) || ($decoded['status'] ?? '') !== 'created') {
            $pendingTransaction->update([
                'status' => 'failed',
                'meta' => array_merge($pendingTransaction->meta ?? [], [
                    'razorpay_order_create_response' => $decoded,
                ]),
            ]);

            return [
                'status' => 400,
                'message' => 'Unable to create Razorpay order.',
                'errors' => $decoded,
            ];
        }

        $pendingTransaction->update([
            'gateway_order_id' => $decoded['id'],
            'meta' => array_merge($pendingTransaction->meta ?? [], [
                'razorpay_order_create_response' => $decoded,
            ]),
        ]);

        return [
            'status' => 200,
            'message' => 'Razorpay order created successfully.',
            'data' => [
                'key' => $credentials['key'],
                'amount' => $decoded['amount'],
                'currency' => $decoded['currency'],
                'razorpay_order_id' => $decoded['id'],
                'local_order_id' => $localOrderId,
                'package_id' => $package->id,
                'package_name' => $package->name,
                'price_rupees' => (int) $package->price_rupees,
                'coins' => (int) $package->coins,
                'wallet' => (int) ($user->wallet ?? 0),
            ],
        ];
    }

    public function verifyRazorpayRecharge(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'razorpay_order_id' => 'required|string',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 400,
                'message' => __('api_msg.please_enter_required_fields'),
            ];
        }

        $credentials = $this->razorpayCredentials();
        if (! $credentials['key'] || ! $credentials['secret']) {
            return [
                'status' => 400,
                'message' => 'Razorpay credentials are not configured.',
            ];
        }

        $pendingTransaction = WalletTransaction::query()
            ->where('gateway_order_id', $request->razorpay_order_id)
            ->where('source', 'recharge')
            ->latest('id')
            ->first();

        if (! $pendingTransaction) {
            return [
                'status' => 400,
                'message' => 'Pending wallet order not found.',
            ];
        }

        $paymentResponse = $this->callRazorpay(
            'https://api.razorpay.com/v1/orders/'.$request->razorpay_order_id.'/payments',
            $credentials,
            null,
            false
        );
        $paymentData = json_decode($paymentResponse, true);
        $capturedPayment = collect($paymentData['items'] ?? [])->first(function ($payment) {
            return in_array($payment['status'] ?? null, ['captured', 'authorized'], true);
        });

        if (! $capturedPayment) {
            return [
                'status' => 400,
                'message' => 'Razorpay payment not completed.',
                'errors' => $paymentData,
            ];
        }

        if ($pendingTransaction->status === 'success') {
            return [
                'status' => 200,
                'message' => 'Wallet recharge already verified.',
                'wallet' => (int) (Users::query()->whereKey($pendingTransaction->user_id)->value('wallet') ?? 0),
                'data' => [
                    'local_order_id' => $pendingTransaction->payment_id,
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $pendingTransaction->gateway_payment_id,
                ],
            ];
        }

        $user = Users::query()->findOrFail($pendingTransaction->user_id);
        $package = CoinPackage::query()->findOrFail($pendingTransaction->coin_package_id);
        $walletTransaction = $this->walletService->completePendingRecharge(
            $user,
            $package,
            $pendingTransaction,
            $request->razorpay_order_id,
            $capturedPayment['id'],
            '',
            [
                'razorpay_status' => $capturedPayment['status'] ?? null,
                'razorpay_method' => $capturedPayment['method'] ?? null,
                'razorpay_response' => $paymentData,
            ]
        );
        $wallet = (int) $walletTransaction->balance_after;

        return [
            'status' => 200,
            'message' => 'Wallet recharged successfully.',
            'wallet' => $wallet,
            'data' => [
                'local_order_id' => $walletTransaction->payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $capturedPayment['id'],
            ],
        ];
    }

    public function balance(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }

        return [
            'wallet_balance' => (int) (Users::query()->whereKey($request->user_id)->value('wallet') ?? 0),
        ];
    }

    public function history(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }

        return [
            'wallet_balance' => (int) (Users::query()->whereKey($request->user_id)->value('wallet') ?? 0),
            'data' => WalletTransaction::query()
                ->where('user_id', $request->user_id)
                ->latest()
                ->get()
                ->map(function (WalletTransaction $transaction): array {
                    return [
                        'id' => $transaction->id,
                        'transaction_type' => $transaction->transaction_type,
                        'source' => $transaction->source,
                        'coins' => (int) $transaction->coins,
                        'balance_before' => (int) $transaction->balance_before,
                        'balance_after' => (int) $transaction->balance_after,
                        'amount_rupees' => $transaction->amount_rupees !== null ? (int) $transaction->amount_rupees : null,
                        'description' => $transaction->description,
                        'payment_id' => $transaction->payment_id,
                        'created_at' => optional($transaction->created_at)->format('Y-m-d H:i:s'),
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    public function unlockReel(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
            'reel_id' => 'required|exists:reel_episodes,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }

        $user = Users::query()->findOrFail($request->user_id);
        $episode = Episode::query()->findOrFail($request->reel_id);

        try {
            $result = $this->walletService->unlockEpisode($user, $episode);

            return [
                'status' => 200,
                'message' => $result['already_unlocked']
                    ? 'Reel already unlocked for this user.'
                    : 'Reel unlocked successfully.',
                'wallet' => (int) $result['wallet_balance'],
                'data' => [
                    'reel_id' => $episode->id,
                    'series_id' => $episode->series_id,
                    'title' => $episode->title,
                    'coin_price' => (int) $episode->coin_price,
                    'is_locked' => (bool) $episode->is_locked,
                    'is_unlocked' => true,
                    'already_unlocked' => (bool) $result['already_unlocked'],
                ],
            ];
        } catch (RuntimeException $exception) {
            return [
                'status' => 400,
                'message' => $exception->getMessage(),
                'wallet' => (int) ($user->wallet ?? 0),
            ];
        }
    }

    private function razorpayCredentials(): array
    {
        $paymentOption = Payment_Option::query()
            ->whereRaw('LOWER(name) = ?', ['razorpay'])
            ->first();

        // $key = $paymentOption?->live_key_1 ?: $paymentOption?->test_key_1 ?: 'rzp_live_kSsZim6DiqXH5y';
        // $secret = $paymentOption?->live_key_2 ?: $paymentOption?->test_key_2 ?: 'c1ZoJNLtALkVgyspFnl68YpY';
        $key = 'rzp_test_SoNrIlMh2ZfqR4';
        $secret = 'Rc56foZlVGEVJdRHEzYwFXEo';

        return [
            'key' => $key,
            'secret' => $secret,
        ];
    }

    private function callRazorpay(string $url, array $credentials, ?string $payload, bool $isPost): string
    {
        $authApiKey = 'Basic '.base64_encode($credentials['key'].':'.$credentials['secret']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: '.$authApiKey,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } else {
            curl_setopt($ch, CURLOPT_POST, false);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return (string) $response;
    }

    private function generateWalletOrderId(int $userId): string
    {
        return 'wallet_'.$userId.'_'.substr(md5(uniqid((string) $userId, true)), 0, 12);
    }
}
