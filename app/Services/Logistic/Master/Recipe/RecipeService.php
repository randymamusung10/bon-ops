<?php

namespace App\Services\Logistic\Master\Recipe;

use App\Repositories\Logistic\Master\Recipe\RecipeRepository;
use App\Models\Logistic\Master\Product\Product;
use App\Helpers\NumberHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecipeService
{
    protected $repository;

    public function __construct(RecipeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function findByUuid($uuid)
    {
        return $this->repository->findByUuid($uuid);
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? 1;
        $data['tenant_id'] = $tenantId;
        $data['company_id'] = $user->company_id ?? 1;

        if (!isset($data['status'])) {
            $data['status'] = 'draft';
        }

        return DB::transaction(function () use ($data, $tenantId) {
            $maxId = $this->repository->getMaxId();
            $data['code'] = 'RCP-' . date('ym') . '-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
            $data['quantity'] = NumberHelper::parse($data['quantity'] ?? 1.0000);

            $recipe = $this->repository->create($data);

            // Process items
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $ingredient = Product::where('id', $item['product_id'])->where('tenant_id', $tenantId)->first();
                    $cost = $ingredient ? (float)$ingredient->cost : 0.00;
                    $qty = NumberHelper::parse($item['quantity']);

                    $this->repository->createItem($recipe, [
                        'product_id' => $item['product_id'],
                        'quantity' => $qty,
                        'unit_id' => $item['unit_id'] ?? ($ingredient ? $ingredient->unit_id : null),
                        'cost' => $cost * $qty,
                    ]);
                }
            }

            // If status is active, deactivate other recipes for this product
            if ($recipe->status === 'active') {
                $this->repository->deactivateOthersForProduct($recipe->product_id, $recipe->id);
            }

            return $recipe;
        });
    }

    public function update($uuid, array $data)
    {
        $recipe = $this->repository->findByUuid($uuid);
        $tenantId = Auth::user()->tenant_id ?? 1;

        return DB::transaction(function () use ($recipe, $data, $tenantId) {
            $data['quantity'] = NumberHelper::parse($data['quantity'] ?? 1.0000);
            
            $this->repository->update($recipe, $data);

            // Sync items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                $this->repository->deleteItems($recipe);
                foreach ($data['items'] as $item) {
                    $ingredient = Product::where('id', $item['product_id'])->where('tenant_id', $tenantId)->first();
                    $cost = $ingredient ? (float)$ingredient->cost : 0.00;
                    $qty = NumberHelper::parse($item['quantity']);

                    $this->repository->createItem($recipe, [
                        'product_id' => $item['product_id'],
                        'quantity' => $qty,
                        'unit_id' => $item['unit_id'] ?? ($ingredient ? $ingredient->unit_id : null),
                        'cost' => $cost * $qty,
                    ]);
                }
            }

            // If status is active, deactivate other recipes for this product
            if ($recipe->status === 'active') {
                $this->repository->deactivateOthersForProduct($recipe->product_id, $recipe->id);
            }

            return $recipe;
        });
    }

    public function delete($uuid)
    {
        $recipe = $this->repository->findByUuid($uuid);
        return DB::transaction(function () use ($recipe) {
            return $this->repository->delete($recipe);
        });
    }
}
