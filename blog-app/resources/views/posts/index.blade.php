<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Blog Posts') }}
            </h2>
            <a href="{{ route('search.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Search Posts
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @auth
                <div class="mb-6">
                    <a href="{{ route('posts.create') }}" 
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create New Post
                    </a>
                </div>
            @endauth

            @forelse($posts as $post)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-2xl font-bold mb-2">
                            <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $post->title }}
                            </a>
                        </h3>
                        
                        <div class="text-sm text-gray-500 mb-4">
                            By {{ $post->user->name }} • {{ $post->created_at->format('M d, Y') }}
                            @if($post->upvotes_count > 0)
                                • {{ $post->upvotes_count }} {{ Str::plural('upvote', $post->upvotes_count) }}
                            @endif
                            @if($post->comments_count > 0)
                                • {{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}
                            @endif
                        </div>
                        
                        @if($post->tags->count() > 0)
                            <div class="mb-4">
                                @foreach($post->tags as $tag)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="text-gray-700">
                            {{ Str::limit($post->content, 200) }}
                        </div>
                        
                        <div class="mt-4 flex justify-between items-center">
                            <a href="{{ route('posts.show', $post) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                Read more →
                            </a>
                            
                            @auth
                                <button onclick="toggleUpvote({{ $post->id }})" 
                                        id="upvote-btn-{{ $post->id }}"
                                        class="flex items-center space-x-1 px-3 py-1 rounded-full text-sm transition-colors
                                               {{ $post->isUpvotedBy(auth()->id()) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    <span>👍</span>
                                    <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes_count }}</span>
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No posts found.</p>
                        @auth
                            <p class="mt-2">
                                <a href="{{ route('posts.create') }}" class="text-blue-600 hover:text-blue-800">
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
