<?php

namespace App\Http\Controllers;

use App\Models\NewsfeedLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsfeedLikeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        NewsfeedLike::create([
            'newsfeed_id' => $request->newsfeed_id,
            'user_id' => Auth::id(),
            'status' => $request->status,
        ]);
    }
}
