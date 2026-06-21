<?php

namespace App\Http\Controllers\System\Settings\Role;

use App\Http\Controllers\Controller;
use App\Services\System\Settings\Role\RoleService;
use App\Http\Requests\System\Settings\Role\StoreRoleRequest;
use App\Http\Requests\System\Settings\Role\UpdateRoleRequest;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        return view('pages.system.settings.roles.index');
    }

    public function data()
    {
        $roles = $this->roleService->getAllForDatatables();
        
        return DataTables::of($roles)
            ->addColumn('permissions_count', function($role) {
                return $role->permissions()->count();
            })
            ->make(true);
    }

    public function create()
    {
        $permissions = \Spatie\Permission\Models\Permission::all();
        return view('pages.system.settings.roles.partials.create_modal', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $this->roleService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil ditambahkan.'
        ]);
    }

    public function show($id)
    {
        $role = \Spatie\Permission\Models\Role::with('permissions')->findOrFail($id);
        return view('pages.system.settings.roles.partials.show_modal', compact('role'));
    }

    public function edit($id)
    {
        $role = \Spatie\Permission\Models\Role::with('permissions')->findOrFail($id);
        $permissions = \Spatie\Permission\Models\Permission::all();
        return view('pages.system.settings.roles.partials.edit_modal', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $this->roleService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diperbarui.'
        ]);
    }

    public function destroy($id)
    {
        $this->roleService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus.'
        ]);
    }
}
