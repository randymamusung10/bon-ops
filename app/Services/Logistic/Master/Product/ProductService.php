<?php

namespace App\Services\Logistic\Master\Product;

use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\Product\ProductPriceHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function create(array $data)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? 1;
        $data['tenant_id'] = $tenantId;
        $data['company_id'] = $user->company_id ?? 1;

        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return DB::transaction(function () use ($data, $tenantId) {
            $maxId = Product::where('tenant_id', $tenantId)->withTrashed()->max('id') ?? 0;
            $data['code'] = 'PRD-' . date('ym') . '-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
            
            // Clean number format for price and cost
            if (isset($data['price'])) {
                $data['price'] = str_replace(['Rp', '.', ',', ' '], ['', '', '.', ''], $data['price']);
            }
            if (isset($data['cost'])) {
                $data['cost'] = str_replace(['Rp', '.', ',', ' '], ['', '', '.', ''], $data['cost']);
            }

            $product = Product::create($data);

            if ($product->price > 0 || $product->cost > 0) {
                ProductPriceHistory::create([
                    'product_id' => $product->id,
                    'old_price' => 0,
                    'new_price' => $product->price,
                    'old_cost' => 0,
                    'new_cost' => $product->cost,
                    'reason' => 'Initial Price Setup'
                ]);
            }

            return $product;
        });
    }

    public function update($uuid, array $data)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $product = Product::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($product, $data) {
            // Clean number format for price and cost
            if (isset($data['price'])) {
                $data['price'] = str_replace(['Rp', '.', ',', ' '], ['', '', '.', ''], $data['price']);
            }
            if (isset($data['cost'])) {
                $data['cost'] = str_replace(['Rp', '.', ',', ' '], ['', '', '.', ''], $data['cost']);
            }

            $newPrice = isset($data['price']) ? (float)$data['price'] : (float)$product->price;
            $newCost = isset($data['cost']) ? (float)$data['cost'] : (float)$product->cost;

            if ((float)$product->price !== $newPrice || (float)$product->cost !== $newCost) {
                ProductPriceHistory::create([
                    'product_id' => $product->id,
                    'old_price' => $product->price,
                    'new_price' => $newPrice,
                    'old_cost' => $product->cost,
                    'new_cost' => $newCost,
                    'reason' => 'Update via Form Edit'
                ]);
            }

            $product->update($data);
            return $product;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $product = Product::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($product) {
            $product->delete();
            return true;
        });
    }
}
