<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{

    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $posts = Post::with(['user', 'comments', 'tags', 'upvotes'])
                    ->withCount('upvotes')
                    ->withCount('comments')
                    ->latest()
                    ->paginate(10);

        $comments = Comment::with(['user', 'post'])
                          ->latest()
                          ->paginate(10);

        $users = User::withCount(['posts', 'comments'])
                    ->latest()
                    ->paginate(10);

        return view('admin.dashboard', compact('posts', 'comments', 'users'));
    }

    /**
     * Delete any post (admin only)
     */
    public function deletePost(Post $post)
    {
        Gate::authorize('delete', $post);
        
        $post->delete();

        return redirect()->back()
            ->with('success', 'Post deleted successfully.');
    }

    /**
     * Delete any comment (admin only)
     */
    public function deleteComment(Comment $comment)
    {
        Gate::authorize('delete', $comment);
        
        $comment->delete();

        return redirect()->back()
            ->with('success', 'Comment deleted successfully.');
    }

    /**
     * Toggle user status (admin only)
     */
    public function toggleUserStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot modify your own account status.');
        }

        $user->update([
            'role' => $user->role === 'admin' ? 'user' : 'admin'
        ]);

        $status = $user->role === 'admin' ? 'promoted to admin' : 'demoted to user';
        
        return redirect()->back()
            ->with('success', "User {$user->name} has been {$status}.");
    }
}