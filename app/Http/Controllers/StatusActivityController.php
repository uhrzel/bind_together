<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class StatusActivityController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $activityId)
    {
        Activity::find($activityId)->update(['is_deleted' => 0]);

        alert()->success('Activity has been updated');
        return redirect()->back();
    }
}
