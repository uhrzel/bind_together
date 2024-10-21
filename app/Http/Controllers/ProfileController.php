<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Organization;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('auth.profile', ['organizations' => Organization::all()]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = auth()->user();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }

            // Store the new avatar and update the user profile
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
        }

        // Update password only if it's provided
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Update address, birthdate, and contact
        $user->update([
            'address' => $request->address,
            'birthdate' => $request->birthdate,
            'contact' => $request->contact,
        ]);

        alert()->success('Profile updated');
        return redirect()->back()->with('success', 'Profile updated.');
    }
}
