<x-app-layout>
    <x-slot name="header">
        <div class="dashboard-header">
            <h2 class="dashboard-title">
                {{ __('Dashboard') }}
            </h2>
            <div class="dashboard-actions">
                <a href="{{ route('posts.create') }}" class="btn btn-primary">
                    Create New Post
                </a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        Admin Panel
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="card-body">
                    <h3 class="welcome-title">
                        Welcome back, {{ auth()->user()->name }}!
                    </h3>
                    <p class="welcome-subtitle">
                        Here's what's happening in your blog community.
                    </p>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="card-body text-center">
                        <h3 class="stat-value">{{ $userPosts->total() }}</h3>
                        <p class="stat-label">Your Posts</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-body text-center">
                        <h3 class="stat-value">{{ $userComments->total() }}</h3>
                        <p class="stat-label">Your Comments</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-body text-center">
                        <h3 class="stat-value">{{ $totalUpvotes }}</h3>
                        <p class="stat-label">Total Upvotes</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-body text-center">
                        <h3 class="stat-value">{{ $recentActivity }}</h3>
                        <p class="stat-label">Recent Activity</p>
                    </div>
                </div>
            </div>

            <!-- Your Recent Posts -->
            <div class="card mb-8">
                <div class="card-header">
                    <div class="recent-posts-header">
                        <h3 class="recent-posts-title">Your Recent Posts</h3>
                        <a href="{{ route('posts.create') }}" class="recent-posts-create">
                            Create New Post
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($userPosts as $post)
                        <div class="recent-post-item">
                            <div class="recent-post-content">
                                <h4 class="recent-post-title">{{ $post->title }}</h4>
                                <p class="recent-post-meta">
                                    {{ $post->created_at->format('M d, Y H:i') }} • 
                                    {{ $post->upvotes_count }} upvotes • 
                                    {{ $post->comments_count }} comments
                                </p>
                            </div>
                            <div class="recent-post-actions">
                                <a href="{{ route('posts.show', $post) }}" class="recent-post-view">
                                    View
                                </a>
                                <a href="{{ route('posts.edit', $post) }}" class="recent-post-edit">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="recent-posts-empty">
                            <p class="recent-posts-empty-text">You haven't created any posts yet.</p>
                            <a href="{{ route('posts.create') }}" class="recent-posts-empty-button">
                                Create Your First Post
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="card mb-8">
                <div class="card-header">
                    <h3 class="recent-comments-title">Your Recent Comments</h3>
                </div>
                <div class="card-body">
                    @forelse($userComments as $comment)
                        <div class="recent-comment-item">
                            <div class="recent-comment-header">
                                <div class="recent-comment-content">
                                    <p class="recent-comment-text">{{ Str::limit($comment->comment, 100) }}</p>
                                    <p class="recent-comment-meta">
                                        On "{{ $comment->post->title }}" • {{ $comment->created_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                                <a href="{{ route('posts.show', $comment->post) }}" class="recent-comment-view">
                                    View Post
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="recent-comments-empty">
                            <p class="recent-comments-empty-text">You haven't commented on any posts yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="quick-actions-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('posts.create') }}" class="quick-action-button btn-primary">
                            <div class="quick-action-icon"></div>
                            <div class="quick-action-text">Create New Post</div>
                        </a>
                        <a href="{{ route('posts.index') }}" class="quick-action-button btn-secondary">
                            <div class="quick-action-icon"></div>
                            <div class="quick-action-text">Browse All Posts</div>
                        </a>
                        <a href="{{ route('search.index') }}" class="quick-action-button btn-warm">
                            <div class="quick-action-icon"></div>
                            <div class="quick-action-text">Search Posts</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
