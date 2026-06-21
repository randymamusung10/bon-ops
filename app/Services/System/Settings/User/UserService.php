<?php

namespace App\Services\System\Settings\User;

use App\Repositories\System\Settings\User\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllForDatatables()
    {
        return $this->userRepository->getAllForDatatables();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ];
            
            if (isset($data['company_id'])) {
                $userData['company_id'] = $data['company_id'];
            }
            
            $user = $this->userRepository->create($userData);
            
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            return $user;
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];
            
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            
            if (isset($data['company_id'])) {
                $userData['company_id'] = $data['company_id'];
            }

            $user = $this->userRepository->update($id, $userData);
            
            $roles = $data['roles'] ?? [];
            $user->syncRoles($roles);

            return $user;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            return $this->userRepository->delete($id);
        });
    }
}
