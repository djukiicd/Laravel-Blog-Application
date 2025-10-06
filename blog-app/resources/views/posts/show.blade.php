<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Post Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-4">
                        <div class="text-sm text-gray-500">
                            By <span class="font-semibold">{{ $post->user->name }}</span> • 
                            {{ $post->created_at->format('M d, Y \a\t g:i A') }}
                            @if($post->updated_at != $post->created_at)
                                • Updated {{ $post->updated_at->format('M d, Y \a\t g:i A') }}
                            @endif
                            @if($post->upvotes_count > 0)
                                • {{ $post->upvotes_count }} {{ Str::plural('upvote', $post->upvotes_count) }}
                            @endif
                        </div>
                        
                        @auth
                            @if(auth()->id() === $post->user_id)
                                <div class="flex space-x-2">
                                    <a href="{{ route('posts.edit', $post) }}" 
                                       class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm"
                                                onclick="return confirm('Are you sure you want to delete this post?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                    
                    @if($post->tags->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Tags:</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($post->tags as $tag)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <div class="prose max-w-none">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                    
                    @auth
                        <div class="mt-6 flex justify-center">
                            <button onclick="toggleUpvote({{ $post->id }})" 
                                    id="upvote-btn-{{ $post->id }}"
                                    class="flex items-center space-x-2 px-6 py-3 rounded-full text-lg transition-colors
                                           {{ $post->isUpvotedBy(auth()->id()) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                <span>👍</span>
                                <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes_count }}</span>
                                <span>Upvote</span>
                            </button>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Comments ({{ $post->comments->count() }})
                    </h3>

                    <!-- Add Comment Form -->
                    @auth
                        <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="mb-4">
                                <textarea name="comment" 
                                          rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('comment') border-red-500 @enderror"
                                          placeholder="Write a comment..."
                                          required>{{ old('comment') }}</textarea>
                                @error('comment')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Comment
                            </button>
                        </form>
                    @else
                        <div class="bg-gray-100 p-4 rounded mb-6 text-center">
                            <p class="text-gray-600">
                                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Login</a> 
                                to leave a comment.
                            </p>
                        </div>
                    @endauth

                    <!-- Comments List -->
                    @forelse($post->comments as $comment)
                        <div class="border-l-4 border-blue-500 pl-4 mb-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="text-sm text-gray-500 mb-1">
                                        <span class="font-semibold">
                                            {{ $comment->user ? $comment->user->name : 'Anonymous' }}
                                        </span>
                                        • {{ $comment->created_at->format('M d, Y \a\t g:i A') }}
                                    </div>
                                    <div class="text-gray-800">
                                        {{ $comment->comment }}
                                    </div>
                                </div>
                                
                                @auth
                                    @if(auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="ml-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-500 hover:text-red-700 text-sm"
                                                    onclick="return confirm('Are you sure you want to delete this comment?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            <p>No comments yet. Be the first to comment!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Back to Posts -->
            <div class="mt-6">
                <a href="{{ route('posts.index') }}" 
                   class="text-blue-600 hover:text-blue-800 font-medium">
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
                    btn.className = btn.className.replace('bg-gray-100 text-gray-600 hover:bg-gray-200', 'bg-green-100 text-green-800');
                } else {
                    btn.className = btn.className.replace('bg-green-100 text-green-800', 'bg-gray-100 text-gray-600 hover:bg-gray-200');
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
