<?php

namespace App\Http\Requests\System\Settings\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $role = $this->route('role');
        $roleId = is_object($role) ? $role->id : $role;
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ];
    }
}
