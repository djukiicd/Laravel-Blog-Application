<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Upvote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpvoteController extends Controller
{
    /**
     * Toggle upvote for a post.
     */
    public function toggle(Post $post)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $upvote = Upvote::where('user_id', $user->id)
                        ->where('post_id', $post->id)
                        ->first();

        if ($upvote) {
            // Remove upvote
            $upvote->delete();
            $isUpvoted = false;
        } else {
            // Add upvote
            Upvote::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $isUpvoted = true;
        }

        $upvoteCount = $post->upvotes()->count();

        return response()->json([
            'isUpvoted' => $isUpvoted,
            'upvoteCount' => $upvoteCount,
        ]);
    }
}
