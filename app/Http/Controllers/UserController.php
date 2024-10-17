<?php

namespace App\Http\Controllers;

use App\Enums\UserTypeEnum;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role', 'super_admin');

        if ($role == UserTypeEnum::ADMINORG) {
            $users = User::role([UserTypeEnum::ADMINSPORT, UserTypeEnum::ADMINORG])->get();
        } else {
            $users = User::role($role)->get();
        }

        return view('super-admin.users.index', compact('users', 'role'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        $user->assignRole($request->role);

        alert()->success('User created successfully');
        return redirect()->route('users.index', ['role' => $request->role]);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return redirect()->route('users.index', ['role' => $user->getRoleNames()->first()]);
    }

    public function destroy(User $user)
    {
        $user->update(['is_active' => 0]);

        alert()->success('User deactived successfully');
        return redirect()->route('users.index', ['role' => $user->getRoleNames()->first()]);
    }

}
