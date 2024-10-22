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
        $request->validate([
            'newsfeed_id' => 'required|exists:newsfeeds,id',
            'status' => 'required|integer|in:0,1',
        ]);

        // Check if the like/dislike already exists
        $newsfeedLike = NewsfeedLike::where('newsfeed_id', $request->newsfeed_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($newsfeedLike) {
            // Update the status if it exists
            $newsfeedLike->update(['status' => $request->status]);
        } else {
            // Create a new like/dislike entry
            NewsfeedLike::create([
                'newsfeed_id' => $request->newsfeed_id,
                'user_id' => Auth::id(),
                'status' => $request->status,
            ]);
        }

        // Get updated like and dislike counts
        $likesCount = NewsfeedLike::where('newsfeed_id', $request->newsfeed_id)
            ->where('status', 1)
            ->count();

        $dislikesCount = NewsfeedLike::where('newsfeed_id', $request->newsfeed_id)
            ->where('status', 0)
            ->count();

        // Return the counts as a JSON response
        return response()->json([
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
        ]);
    }

}
