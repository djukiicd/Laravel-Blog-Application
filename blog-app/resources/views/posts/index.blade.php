<x-app-layout>
    <x-slot name="header">
        <div class="posts-header">
            <h2 class="posts-title">
                {{ __('Blog Posts') }}
            </h2>
            <a href="{{ route('search.index') }}" class="posts-search-link">
                Search Posts
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            @auth
                <div class="posts-create-section">
                    <a href="{{ route('posts.create') }}" class="posts-create-button">
                        Create New Post
                    </a>
                </div>
            @endauth

            @forelse($posts as $post)
                <div class="post-item mb-6">
                    <div class="card-body">
                        <h3 class="post-title">
                            <a href="{{ route('posts.show', $post) }}" class="post-title-link">
                                {{ $post->title }}
                            </a>
                        </h3>
                        
                        <div class="post-meta">
                            <span>By {{ $post->user->name }}</span>
                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                            @if($post->upvotes_count > 0)
                                <span>{{ $post->upvotes_count }} {{ Str::plural('upvote', $post->upvotes_count) }}</span>
                            @endif
                            @if($post->comments_count > 0)
                                <span>{{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}</span>
                            @endif
                        </div>
                        
                        @if($post->tags->count() > 0)
                            <div class="post-tags">
                                @foreach($post->tags as $tag)
                                    <span class="post-tag">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="post-excerpt">
                            {{ Str::limit($post->content, 200) }}
                        </div>
                        
                        <div class="post-actions">
                            <a href="{{ route('posts.show', $post) }}" 
                               class="post-read-more">
                                Read more →
                            </a>
                            
                            <div class="post-action-buttons">
                                @auth
                                    <button onclick="toggleUpvote({{ $post->id }})" 
                                            id="upvote-btn-{{ $post->id }}"
                                            class="{{ $post->isUpvotedBy(auth()->id()) ? 'upvote-button-active' : 'upvote-button-inactive' }}">
                                        <span>👍</span>
                                        <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes_count }}</span>
                                    </button>
                                @endauth
                                
                                @can('delete', $post)
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-button" 
                                                onclick="return confirm('Are you sure you want to delete this post?')">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="posts-empty">
                    <div class="posts-empty-content">
                        <p class="posts-empty-title">No posts found.</p>
                        @auth
                            <p class="mt-2">
                                <a href="{{ route('posts.create') }}" class="posts-empty-link">
                                    Create the first post!
                                </a>
                            </p>
                        @endauth
                    </div>
                </div>
            @endforelse

            {{ $posts->links() }}
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
                    btn.className = btn.className.replace('upvote-button-inactive', 'upvote-button-active');
                } else {
                    btn.className = btn.className.replace('upvote-button-active', 'upvote-button-inactive');
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
