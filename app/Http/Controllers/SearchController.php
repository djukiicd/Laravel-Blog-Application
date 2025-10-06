<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search posts by query and tags.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $tagId = $request->get('tag');
        
        $posts = Post::with(['user', 'tags', 'upvotes'])
                    ->withCount('upvotes')
                    ->withCount('comments');

        // Search by query
        if ($query) {
            $posts->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            });
        }

        // Filter by tag
        if ($tagId) {
            $posts->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        $posts = $posts->orderBy('created_at', 'desc')->paginate(10);
        
        // Get all tags for the filter dropdown
        $tags = Tag::orderBy('name')->get();

        return view('search.results', compact('posts', 'query', 'tags', 'tagId'));
    }

    /**
     * Show search form and results.
     */
    public function index(Request $request)
    {
        $tags = Tag::orderBy('name')->get();
        $query = $request->get('q', '');
        $tagId = $request->get('tag');
        
        // Always create a query builder, but only execute if there are search parameters
        $postsQuery = Post::with(['user', 'tags', 'upvotes'])
                          ->withCount('upvotes')
                          ->withCount('comments');

        // Search by query
        if ($query) {
            $postsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            });
        }

        // Filter by tag
        if ($tagId) {
            $postsQuery->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        // Only paginate if there are search parameters, otherwise return empty paginated result
        if ($query || $tagId) {
            $posts = $postsQuery->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $posts = $postsQuery->orderBy('created_at', 'desc')->paginate(10);
            $posts->setCollection(collect()); // Empty collection
        }

        return view('search.index', compact('tags', 'posts', 'query', 'tagId'));
    }
}
