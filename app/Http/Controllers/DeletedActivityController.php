<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeletedActivityController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (auth()->user()->hasRole('admin_sport') || auth()->user()->hasRole('admin_org') || auth()->user()->hasRole('super_admin'))
        {
            $activities = Activity::where('is_deleted', 1)->get();
        } else {
            $activities = Activity::where('user_id', Auth::id())
                ->where('is_deleted', 1)->get();
        }

        return view('admin-sport.activity.deleted', [
            'activities' => $activities,
            'user' => Auth::user()->load('organization'),
        ]);
    }
}
