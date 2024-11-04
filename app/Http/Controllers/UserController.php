<?php

namespace App\Http\Controllers;

use App\Enums\UserTypeEnum;
use App\Http\Requests\StoreUserRequest;
use App\Mail\VerifyUserEmail;
use App\Models\Organization;
use App\Models\Sport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

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

        $sports = Sport::all();
        $organizations = Organization::all();

        return view('super-admin.users.index', compact('users', 'role', 'sports', 'organizations'));
    }

    public function store(StoreUserRequest $request)
    {
        $payload = $request->validated();

        if (isset($payload['organization_id']) && $payload['organization_id'] == 'select_other') {
            $created = Organization::create([
                "name" => $payload['txtAddSelectOther']
            ]);
            if ($created) {
                $payload['organization_id'] = $created->id;
            }
        }

        $user = User::create($payload);

        $user->assignRole($payload['role']);

        $verificationUrl = $this->generateVerificationUrl($user);

        Mail::to($user->email)->send(new VerifyUserEmail(
            $user,
            $verificationUrl,
            $payload['password'],
            $payload['role']
        ));

        alert()->success('User created successfully');
        return redirect()->back();
    }

    protected function generateVerificationUrl($user)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );
    }

    public function show(User $user)
    {
        $user->load('sport', 'organization', 'campus', 'program', 'course', 'roles');

        return response()->json(['user' => $user, 'roles' => $user->getRoleNames(), 'organizations' => $user->organization()]);
    }

    public function update(Request $request, User $user)
    {
        if ($request->organization_id == 'select_other') {
            $created = Organization::create([
                "name" => $request->txtSelectOther
            ]);
            if ($created) {
                $request['organization_id'] = $created['id'];
            }
        }

        if ($request->filled(['password', 'password_confirmation'])) {
            $request->validate([
                'password' => 'required|confirmed|min:8', // Validation rules
            ]);

            // Update the password if both fields are provided
            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // Update other fields that don't include password
        $user->update($request->except(['password', 'password_confirmation']));

        return redirect()->back();
    }

    public function destroy(User $user)
    {
        $user->update(['is_active' => 0]);

        alert()->success('User deactived successfully');
        return redirect()->route('users.index', ['role' => $user->getRoleNames()->first()]);
    }
}
