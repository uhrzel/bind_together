<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Http\Requests\StoreActivityRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $user = Auth::user()->load('organization');

    if ($user->hasRole('super_admin') || $user->hasRole('admin_sport')) {
        // Fetch only Tryouts and Competitions for admin_sport
        $activities = Activity::with(['sport', 'user']) // Load related models
            ->whereIn('status', [0, 1]) // Only include activities that are Pending or Approved
            ->whereIn('type', [
                ActivityType::Tryout, 
                ActivityType::Competition
            ])
            ->get();
    } else {
        // For non-admin roles, show all their activities
        $activities = Activity::with(['sport', 'user']) // Load related models
            ->where('user_id', $user->id)
            ->whereIn('status', [0, 1])
            ->get();
    }

    return view('admin-sport.activity.index', [
        'activities' => $activities,
        'user' => $user,
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request)
    {
        $status = 0;

        if (in_array($request->input('type'), [2, 3, 0])) {
            $status = 1;
        }

        if (auth()->user()->hasRole(['admin_org', 'admin_sport'])) {
            $status = 1;
        }

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $data = $request->except('attachment') + [
                'attachment' => $path,
                'user_id' => Auth::id(),
                'status' => $status
            ];
        } else {
            $data = $request->validated() + [
                'status' => $status,
                'user_id' => Auth::id()
            ];
        }

        Activity::create($data);

        alert()->success('Activity created successfully');
        return redirect()->route('activity.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        $activity->load('sport');
        return response()->json($activity);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
 * Update the specified resource in storage.
 */
public function update(StoreActivityRequest $request, Activity $activity)
{
    
    if ($request->input('type') == ActivityType::Competition) {
        $activity->status = 1; 
    } else {
        
        $activity->status = 0; 
    }

    $activity->update($request->validated() + ['status' => $activity->status]);

    alert()->success('Activity updated successfully');
    return redirect()->route('activity.index');
}
}

