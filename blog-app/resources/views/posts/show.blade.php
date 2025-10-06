<x-app-layout>
    <x-slot name="header">
        <h2 class="post-show-header">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="post-show-container">
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Post Content -->
            <div class="post-content-card">
                <div class="post-content-body">
                    <div class="post-meta-header">
                        <div class="post-meta-info">
                            By <span class="post-meta-author">{{ $post->user->name }}</span> • 
                            {{ $post->created_at->format('M d, Y \a\t g:i A') }}
                            @if($post->updated_at != $post->created_at)
                                • Updated {{ $post->updated_at->format('M d, Y \a\t g:i A') }}
                            @endif
                            @if($post->upvotes_count > 0)
                                • {{ $post->upvotes_count }} {{ Str::plural('upvote', $post->upvotes_count) }}
                            @endif
                        </div>
                        
                        @can('update', $post)
                            <div class="post-actions-header">
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warm text-xs">
                                    Edit
                                </a>
                                @can('delete', $post)
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="delete-button"
                                                onclick="return confirm('Are you sure you want to delete this post?')">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @endcan
                    </div>
                    
                    @if($post->tags->count() > 0)
                        <div class="post-tags-section">
                            <h4 class="post-tags-title">Tags:</h4>
                            <div class="post-tags-container">
                                @foreach($post->tags as $tag)
                                    <span class="post-tag-large">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <div class="post-content-prose">
                        {!! $post->content !!}
                    </div>
                    
                    @auth
                        <div class="post-upvote-section">
                            <button onclick="toggleUpvote({{ $post->id }})" 
                                    id="upvote-btn-{{ $post->id }}"
                                    class="{{ $post->isUpvotedBy(auth()->id()) ? 'post-upvote-button-active' : 'post-upvote-button-inactive' }}">
                                <span>👍</span>
                                <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes_count }}</span>
                                <span>Upvote</span>
                            </button>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Comments Section -->
            <div class="comments-card">
                <div class="comments-body">
                    <h3 class="comments-title">
                        Comments ({{ $post->comments->count() }})
                    </h3>

                    <!-- Add Comment Form -->
                    @auth
                        <form action="{{ route('comments.store', $post) }}" method="POST" class="comment-form">
                            @csrf
                            <div class="mb-4">
                                <textarea name="comment" 
                                          rows="3" 
                                          class="@error('comment') comment-textarea-error @else comment-textarea @enderror"
                                          placeholder="Write a comment..."
                                          required>{{ old('comment') }}</textarea>
                                @error('comment')
                                    <p class="comment-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" 
                                    class="comment-submit-button">
                                Add Comment
                            </button>
                        </form>
                    @else
                        <div class="login-prompt">
                            <p class="login-prompt-text">
                                <a href="{{ route('login') }}" class="login-prompt-link">Login</a> 
                                to leave a comment.
                            </p>
                        </div>
                    @endauth

                    <!-- Comments List -->
                    @forelse($post->comments as $comment)
                        <div class="comment-item">
                            <div class="comment-header">
                                <div class="comment-content-wrapper">
                                    <div class="comment-meta">
                                        <span class="comment-author">
                                            {{ $comment->user ? $comment->user->name : 'Anonymous' }}
                                        </span>
                                        • {{ $comment->created_at->format('M d, Y \a\t g:i A') }}
                                    </div>
                                    <div class="comment-text">
                                        {{ $comment->comment }}
                                    </div>
                                </div>
                                
                                @can('delete', $comment)
                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="comment-actions">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="delete-button"
                                                onclick="return confirm('Are you sure you want to delete this comment?')">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="comments-empty">
                            <p>No comments yet. Be the first to comment!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Back to Posts -->
            <div class="back-to-posts">
                <a href="{{ route('posts.index') }}" 
                   class="back-to-posts-link">
                    ← Back to all posts
                </a>
            </div>
        </div>
    </div>

    @auth
    <script>
        function toggleUpvote(postId) {
            fetch(`/posts/${postId}/upvote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const btn = document.getElementById(`upvote-btn-${postId}`);
                const count = document.getElementById(`upvote-count-${postId}`);
                
                if (data.isUpvoted) {
                    btn.className = btn.className.replace('post-upvote-button-inactive', 'post-upvote-button-active');
                } else {
                    btn.className = btn.className.replace('post-upvote-button-active', 'post-upvote-button-inactive');
                }
                
                count.textContent = data.upvoteCount;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
    @endauth
</x-app-layout>
