<?php

namespace App\Http\Controllers\System\Settings\User;

use App\Http\Controllers\Controller;
use App\Services\System\Settings\User\UserService;
use App\Http\Requests\System\Settings\User\StoreUserRequest;
use App\Http\Requests\System\Settings\User\UpdateUserRequest;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return view('pages.system.settings.users.index');
    }

    public function data()
    {
        $users = $this->userService->getAllForDatatables();
        
        return DataTables::of($users)
            ->addColumn('roles', function($user) {
                return $user->roles->pluck('name')->implode(', ');
            })
            ->make(true);
    }

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $companies = \App\Models\Company::where('tenant_id', auth()->user()->tenant_id ?? 1)->get();
        return view('pages.system.settings.users.partials.create_modal', compact('roles', 'companies'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan.'
        ]);
    }

    public function show($id)
    {
        $user = \App\Models\User::with(['roles', 'company'])->findOrFail($id);
        return view('pages.system.settings.users.partials.show_modal', compact('user'));
    }

    public function edit($id)
    {
        $user = \App\Models\User::with('roles')->findOrFail($id);
        $roles = \Spatie\Permission\Models\Role::all();
        $companies = \App\Models\Company::where('tenant_id', auth()->user()->tenant_id ?? 1)->get();
        
        return view('pages.system.settings.users.partials.edit_modal', compact('user', 'roles', 'companies'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $this->userService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui.'
        ]);
    }

    public function destroy($id)
    {
        $this->userService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.'
        ]);
    }
}
