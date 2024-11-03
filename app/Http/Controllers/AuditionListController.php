<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Models\ActivityRegistration;
use Illuminate\Http\Request;

class AuditionListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Get status from the request, defaulting to '0' if not provided
        $status = $request->query('status', '0');

        // Fetch audition registrations with related activity and user details
        $auditions = ActivityRegistration::query()
            ->with(['activity', 'user'])
            ->where('status', $status)
            ->whereHas('activity', function ($query) {
                $query->where('type', ActivityType::Audition);
            })
            ->get();

        // Return view with auditions and the status filter
        return view('adviser.performer-record.index', ['auditions' => $auditions, 'status' => $status]);
    }
}
