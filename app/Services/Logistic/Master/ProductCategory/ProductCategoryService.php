<?php

namespace App\Services\Logistic\Master\ProductCategory;

use App\Models\Logistic\Master\ProductCategory\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductCategoryService
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
            $maxId = ProductCategory::where('tenant_id', $tenantId)->withTrashed()->max('id') ?? 0;
            $data['code'] = 'CAT-' . date('ym') . '-' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT);
            return ProductCategory::create($data);
        });
    }

    public function update($uuid, array $data)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $category = ProductCategory::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($category, $data) {
            $category->update($data);
            return $category;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $category = ProductCategory::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($category) {
            $category->delete();
            return true;
        });
    }
    
    public function getForSelect2(?string $search, ?string $excludeUuid = null)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $query = ProductCategory::where('tenant_id', $tenantId)
            ->where('status', 'active');
            
        if ($excludeUuid) {
            $query->where('uuid', '!=', $excludeUuid);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $categories = $query->limit(20)->get();

        return $categories->map(function($category) {
            return [
                'id' => $category->id,
                'text' => '[' . $category->code . '] ' . $category->name
            ];
        });
    }
}
