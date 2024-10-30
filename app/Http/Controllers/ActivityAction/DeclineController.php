<?php

namespace App\Http\Controllers\ActivityAction;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class DeclineController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Activity $activity)
    {
        $activity->update(
            [
                'status' => 2,
            ]
        );

        // Mail::to($user->email)->send(new VerifyUserEmail(
        //     $user,
        //     $verificationUrl,
        //     $request->password,
        //     $request->role
        // ));

        alert()->success('Activity Approve Successfully!');

        return redirect()->route('activity.index');
    }
}
