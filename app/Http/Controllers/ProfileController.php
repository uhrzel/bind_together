<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Campus;
use App\Models\Organization;
use App\Models\Program;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('auth.profile', [
            'organizations' => Organization::all(),
            'campuses' => Campus::all(),
            'programs' => Program::all()
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = auth()->user();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists in storage
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }

            // Store the new avatar and update the user profile
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
        }

        // Update password only if provided
        if ($request->filled('new_password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Update other profile fields including address, birthdate, contact, campus, program, and year level
        $user->update([
            'address' => $request->address,
            'birthdate' => $request->birthdate,
            'contact' => $request->contact,
            'campus_id' => $request->campus_id,
            'program_id' => $request->program_id,
            'year_level' => $request->year_level,
        ]);

        // Success message with SweetAlert and redirection
        alert()->success('Profile updated successfully!');
        return redirect()->back()->with('success', 'Profile updated.');
    }

}
