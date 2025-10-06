<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-primary-300 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                    View Posts
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-warm">
                    User Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-2xl font-bold text-primary-300">{{ $posts->total() }}</h3>
                        <p class="text-accent-300">Total Posts</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-2xl font-bold text-primary-300">{{ $comments->total() }}</h3>
                        <p class="text-accent-300">Total Comments</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-2xl font-bold text-primary-300">{{ $users->total() }}</h3>
                        <p class="text-accent-300">Total Users</p>
                    </div>
                </div>
            </div>

            <!-- Recent Posts -->
            <div class="card mb-8">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-primary-300">Recent Posts</h3>
                </div>
                <div class="card-body">
                    @forelse($posts as $post)
                        <div class="flex justify-between items-center py-3 border-b border-warm-200 last:border-b-0">
                            <div class="flex-1">
                                <h4 class="font-medium text-primary-300">{{ $post->title }}</h4>
                                <p class="text-sm text-accent-300">By {{ $post->user->name }} • {{ $post->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-warm text-xs">
                                    View
                                </a>
                                <form action="{{ route('admin.posts.delete', $post) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger text-xs" 
                                            onclick="return confirm('Are you sure you want to delete this post?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-accent-300 text-center py-4">No posts found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="card mb-8">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-primary-300">Recent Comments</h3>
                </div>
                <div class="card-body">
                    @forelse($comments as $comment)
                        <div class="flex justify-between items-start py-3 border-b border-warm-200 last:border-b-0">
                            <div class="flex-1">
                                <p class="text-primary-300">{{ Str::limit($comment->content, 100) }}</p>
                                <p class="text-sm text-accent-300">
                                    By {{ $comment->user->name }} on "{{ $comment->post->title }}" • {{ $comment->created_at->format('M d, Y H:i') }}
                                </p>
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <a href="{{ route('posts.show', $comment->post) }}" class="btn btn-warm text-xs">
                                    View Post
                                </a>
                                <form action="{{ route('admin.comments.delete', $comment) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger text-xs" 
                                            onclick="return confirm('Are you sure you want to delete this comment?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-accent-300 text-center py-4">No comments found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Users Management -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-primary-300">Users Management</h3>
                </div>
                <div class="card-body">
                    @forelse($users as $user)
                        <div class="flex justify-between items-center py-3 border-b border-warm-200 last:border-b-0">
                            <div class="flex-1">
                                <h4 class="font-medium text-primary-300">{{ $user->name }}</h4>
                                <p class="text-sm text-accent-300">
                                    {{ $user->email }} • 
                                    <span class="px-2 py-1 rounded-full text-xs {{ $user->role === 'admin' ? 'bg-primary-300 text-primary-50' : 'bg-accent-200 text-primary-50' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    • {{ $user->posts_count }} posts • {{ $user->comments_count }} comments
                                </p>
                            </div>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn {{ $user->role === 'admin' ? 'btn-danger' : 'btn-primary' }} text-xs" 
                                            onclick="return confirm('Are you sure you want to change this user\'s role?')">
                                        {{ $user->role === 'admin' ? 'Demote to User' : 'Promote to Admin' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-accent-300 text-xs">Current User</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-accent-300 text-center py-4">No users found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
