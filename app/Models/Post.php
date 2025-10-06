<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    /**
     * Get the user that owns the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the post.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the upvotes for the post.
     */
    public function upvotes()
    {
        return $this->hasMany(Upvote::class);
    }

    /**
     * Get the tags for the post.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the upvote count for the post.
     */
    public function getUpvoteCountAttribute()
    {
        return $this->upvotes()->count();
    }

    /**
     * Check if the authenticated user has upvoted this post.
     */
    public function isUpvotedBy($userId)
    {
        return $this->upvotes()->where('user_id', $userId)->exists();
    }
}
