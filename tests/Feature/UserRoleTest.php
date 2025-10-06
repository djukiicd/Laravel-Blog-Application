<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users with different roles
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->otherUser = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function admin_users_have_admin_role()
    {
        $this->assertTrue($this->admin->isAdmin());
        $this->assertFalse($this->admin->isUser());
    }

    /** @test */
    public function regular_users_have_user_role()
    {
        $this->assertTrue($this->user->isUser());
        $this->assertFalse($this->user->isAdmin());
    }

    /** @test */
    public function admins_can_access_admin_features()
    {
        $this->actingAs($this->admin);
        
        // Admin should be able to edit any post
        $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
        $response = $this->get("/posts/{$post->id}/edit");
        $response->assertStatus(200);
    }

    /** @test */
    public function admins_can_delete_any_post()
    {
        $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
        
        $this->actingAs($this->admin);
        
        $response = $this->delete("/posts/{$post->id}");
        $response->assertRedirect('/posts');
        
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /** @test */
    public function admins_can_delete_any_comment()
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_id' => $post->id
        ]);
        
        $this->actingAs($this->admin);
        
        $response = $this->delete("/comments/{$comment->id}");
        $response->assertRedirect("/posts/{$post->id}");
        
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function regular_users_cannot_access_admin_features()
    {
        $this->actingAs($this->user);
        
        // Regular user should not be able to edit other users' posts
        $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
        $response = $this->get("/posts/{$post->id}/edit");
        $response->assertStatus(403);
    }

    /** @test */
    public function regular_users_cannot_delete_other_users_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
        
        $this->actingAs($this->user);
        
        $response = $this->delete("/posts/{$post->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function regular_users_cannot_delete_other_users_comments()
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_id' => $post->id
        ]);
        
        $this->actingAs($this->user);
        
        $response = $this->delete("/comments/{$comment->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function post_owners_can_manage_their_own_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        
        $this->actingAs($this->user);
        
        // Can edit their own post
        $response = $this->get("/posts/{$post->id}/edit");
        $response->assertStatus(200);
        
        // Can delete their own post
        $response = $this->delete("/posts/{$post->id}");
        $response->assertRedirect('/posts');
    }

    /** @test */
    public function comment_owners_can_manage_their_own_comments()
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'post_id' => $post->id
        ]);
        
        $this->actingAs($this->user);
        
        // Can delete their own comment
        $response = $this->delete("/comments/{$comment->id}");
        $response->assertRedirect("/posts/{$post->id}");
    }

    /** @test */
    public function post_owners_can_delete_comments_on_their_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $comment = Comment::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_id' => $post->id
        ]);
        
        $this->actingAs($this->user);
        
        // Post owner can delete comments on their posts
        $response = $this->delete("/comments/{$comment->id}");
        $response->assertRedirect("/posts/{$post->id}");
    }

    /** @test */
    public function role_is_properly_stored_in_database()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin'
        ]);
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'user'
        ]);
    }

    /** @test */
    public function default_role_is_user()
    {
        $user = User::factory()->create();
        
        $this->assertEquals('user', $user->role);
        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
    }
}