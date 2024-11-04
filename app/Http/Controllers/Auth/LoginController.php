<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        // Redirect students to the newsfeed
        if (auth()->user()->hasRole('student')) {
            return route('newsfeed.index');
        }

        // Default redirect to home if user is not a student
        return '/home';
    }

    /**
     * Handle post-authentication logic and redirection.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if the user account is active
        if (!auth()->user()->is_active) {
            auth()->logout();
            alert()->error('Deactivated Account', 'You no longer have access to your account. Please contact the admin at bpsu.bindtogether@gmail.com if you have any concerns. Thank you!');
            return redirect()->route('login');
        }

        // Check if the user profile is completed
        if ($user->is_completed != 1) {
            return redirect()->route('profile.completion');
        }

        // Trigger SweetAlert after successful login
        alert()->success('Sign in successful', 'Redirecting...');
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
