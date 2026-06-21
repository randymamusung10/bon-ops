<?php

namespace App\Repositories\System\Settings\Role;

use Spatie\Permission\Models\Role;

class RoleRepository
{
    public function getAllForDatatables()
    {
        return Role::query();
    }

    public function findById($id)
    {
        return Role::findOrFail($id);
    }

    public function create(array $data)
    {
        return Role::create($data);
    }

    public function update($id, array $data)
    {
        $role = $this->findById($id);
        $role->update($data);
        return $role;
    }

    public function delete($id)
    {
        $role = $this->findById($id);
        return $role->delete();
    }
}
