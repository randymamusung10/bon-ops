<?php

namespace App\Repositories\System\Settings\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository
{
    public function getAllForDatatables()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        return User::with('roles')
            ->where('tenant_id', $tenantId)
            ->latest();
    }

    public function findById($id)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        return User::where('tenant_id', $tenantId)->findOrFail($id);
    }

    public function create(array $data)
    {
        $data['tenant_id'] = Auth::user()->tenant_id ?? 1;
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = $this->findById($id);
        return $user->delete();
    }
}
