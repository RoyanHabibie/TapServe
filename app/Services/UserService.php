<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAll(int $shopId)
    {
        return User::where('shop_id', $shopId)->latest()->get();
    }

    public function store(int $shopId, array $data): User
    {
        return User::create([
            'shop_id'  => $shopId,
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);
    }

    public function update(User $user, array $data): User
    {
        $payload = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
