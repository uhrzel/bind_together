<?php

namespace App\Http\Controllers;

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
        return view('admin-sport.activity.index', [
            'activities' => Activity::where('user_id', Auth::id())
                ->whereIn('status', [0, 2])->get(),
            'user' => Auth::user()->load('organization')
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
        if ($request->hasFile('attachment')) {
            $paths = [];

            foreach ($request->file('attachment') as $file) {
                $path = $file->store('attachments', 'public');
                $paths[] = $path;
            }

            $data = $request->except('attachment') + ['attachment' => json_encode($paths), 'user_id' => Auth::id(), 'status' => 0];
        } else {
            $data = $request->validated();
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
        $activity->update($request->validated() + ['status' => 0]);

        alert()->success('Activity updated successfully');
        return redirect()->route('activity.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        //
    }
}
