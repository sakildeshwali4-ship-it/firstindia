<?php

namespace App\Http\Controllers\Admin\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\CoinPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function edit(): View
    {
        return view('admin.reels.wallet.edit', [
            'packages' => CoinPackage::query()
                ->orderBy('sort_order')
                ->orderBy('price_rupees')
                ->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $deleteIds = collect($request->input('delete_package_ids', []))
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $request->merge([
            'packages' => collect($request->input('packages', []))
                ->filter(fn (array $package): bool => filled($package['name'] ?? null) || filled($package['coins'] ?? null) || filled($package['price_rupees'] ?? null))
                ->values()
                ->all(),
        ]);
        $data = $request->validate([
            'packages' => ['nullable', 'array'],
            'packages.*.id' => ['nullable', 'integer', 'exists:coin_packages,id'],
            'packages.*.name' => ['required_with:packages', 'string', 'max:120'],
            'packages.*.coins' => ['required_with:packages', 'integer', 'min:1', 'max:10000000'],
            'packages.*.price_rupees' => ['required_with:packages', 'integer', 'min:1', 'max:10000000'],
            'packages.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'packages.*.is_active' => ['nullable', 'boolean'],
        ]);

        if ($deleteIds->isNotEmpty()) {
            CoinPackage::query()->whereIn('id', $deleteIds)->delete();
        }

        foreach ($data['packages'] ?? [] as $packageData) {
            $values = [
                'name' => $packageData['name'],
                'coins' => $packageData['coins'],
                'bonus_coins' => 0,
                'price_rupees' => $packageData['price_rupees'],
                'sort_order' => $packageData['sort_order'] ?? 0,
                'is_active' => (bool) ($packageData['is_active'] ?? false),
            ];

            if (! empty($packageData['id'])) {
                if ($deleteIds->contains((int) $packageData['id'])) {
                    continue;
                }

                CoinPackage::query()->whereKey($packageData['id'])->update($values);

                continue;
            }

            CoinPackage::query()->create($values);
        }

        return redirect()
            ->route('wallet.edit')
            ->with('status', 'Wallet coin settings updated.');
    }
}
