<?php

namespace App\Services\System\Settings\Role;

use App\Repositories\System\Settings\Role\RoleRepository;
use Illuminate\Support\Facades\DB;

class RoleService
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAllForDatatables()
    {
        return $this->roleRepository->getAllForDatatables();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $roleData = [
                'name' => $data['name'],
                'guard_name' => 'web'
            ];
            $role = $this->roleRepository->create($roleData);
            
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role;
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $roleData = [
                'name' => $data['name']
            ];
            $role = $this->roleRepository->update($id, $roleData);
            
            $permissions = $data['permissions'] ?? [];
            $role->syncPermissions($permissions);

            return $role;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            return $this->roleRepository->delete($id);
        });
    }
}
