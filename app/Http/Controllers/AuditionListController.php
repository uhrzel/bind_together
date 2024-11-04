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
        $status = $request->query('status', '0');

        $type   = null;

        if ($request->has('type')) {
            $type = $request->query('type');
        }

        $auditions = ActivityRegistration::query()
            ->with(['activity', 'user'])
            ->where('status', $status);

        $auditions = $auditions->whereHas('activity', function ($query) use ($type) {
            if ($type == '3') {
                $query->where('type', ActivityType::Competition);
            } else {
                $query->where('type', ActivityType::Audition);
            }
        });

        $auditions = $auditions->get();

        return view('adviser.performer-record.index', ['auditions' => $auditions, 'status' => $status]);
    }
}
