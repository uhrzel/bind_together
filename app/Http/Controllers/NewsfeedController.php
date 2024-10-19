<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsfeedRequest;
use App\Models\Newsfeed;
use App\Models\NewsfeedFile;
use App\Models\ReportedComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsfeedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('student.newsfeed.index', ['newsfeeds' => Newsfeed::with('user', 'newsfeedFiles', 'comments.user')->get()]);
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
    public function store(StoreNewsfeedRequest $request)
    {
        $newsfeed = Newsfeed::create([
            'user_id' => Auth::id(),
            'description' => $request->description,
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $filePath = $file->store('newsfeed_files', 'public'); // Storing in 'public/newsfeed_files'
    
                $fileType = $file->getMimeType();
    
                NewsfeedFile::create([
                    'newsfeed_id' => $newsfeed->id,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                ]);
            }
        }

        alert()->success('Posted successfully');
        return redirect()->route('newsfeed.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Newsfeed $newsfeed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Newsfeed $newsfeed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Newsfeed $newsfeed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsfeed $newsfeed)
    {
        //
    }
}
