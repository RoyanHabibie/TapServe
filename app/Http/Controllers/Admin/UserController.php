<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index()
    {
        $users = $this->userService->getAll(auth()->user()->shop_id);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(UserStoreRequest $request)
    {
        $this->userService->store(auth()->user()->shop_id, $request->validated());
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        abort_if($user->shop_id !== auth()->user()->shop_id, 403);
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        abort_if($user->shop_id !== auth()->user()->shop_id, 403);
        $this->userService->update($user, $request->validated());
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->shop_id !== auth()->user()->shop_id, 403);
        abort_if($user->id === auth()->id(), 403, 'Tidak dapat menghapus akun sendiri.');
        $this->userService->delete($user);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
