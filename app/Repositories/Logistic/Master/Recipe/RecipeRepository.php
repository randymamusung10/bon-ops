<?php

namespace App\Repositories\Logistic\Master\Recipe;

use App\Models\Logistic\Master\Recipe\Recipe;
use App\Models\Logistic\Master\Recipe\RecipeItem;
use Illuminate\Support\Facades\Auth;

class RecipeRepository
{
    public function getQuery()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        return Recipe::where('tenant_id', $tenantId);
    }

    public function getAll()
    {
        return $this->getQuery()->with(['product', 'station'])->get();
    }

    public function findByUuid($uuid)
    {
        return $this->getQuery()->where('uuid', $uuid)->with(['product', 'station', 'items.product', 'items.unit'])->firstOrFail();
    }

    public function create(array $data)
    {
        return Recipe::create($data);
    }

    public function update(Recipe $recipe, array $data)
    {
        $recipe->update($data);
        return $recipe;
    }

    public function delete(Recipe $recipe)
    {
        return $recipe->delete();
    }

    public function getMaxId()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        return Recipe::where('tenant_id', $tenantId)->withTrashed()->max('id') ?? 0;
    }

    public function deactivateOthersForProduct($productId, $exceptRecipeId = null)
    {
        $query = $this->getQuery()->where('product_id', $productId);
        if ($exceptRecipeId) {
            $query->where('id', '!=', $exceptRecipeId);
        }
        $query->update(['status' => 'inactive']);
    }

    public function createItem(Recipe $recipe, array $itemData)
    {
        return $recipe->items()->create($itemData);
    }

    public function deleteItems(Recipe $recipe)
    {
        return $recipe->items()->delete();
    }
}
