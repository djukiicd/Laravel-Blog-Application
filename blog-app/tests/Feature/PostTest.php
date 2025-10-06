<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->otherUser = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function guests_can_view_posts_index()
    {
        $response = $this->get('/posts');
        $response->assertStatus(200);
        $response->assertViewIs('posts.index');
    }

    /** @test */
    public function guests_can_view_single_post()
    {
        $post = Post::factory()->create();
        
        $response = $this->get("/posts/{$post->id}");
        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        $response->assertSee($post->title);
    }

    /** @test */
    public function authenticated_users_can_create_posts()
    {
        $this->actingAs($this->user);
        
        $postData = [
            'title' => 'Test Post Title',
            'content' => 'This is the content of the test post.'
        ];
        
        $response = $this->post('/posts', $postData);
        $response->assertRedirect('/posts');
        
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'content' => 'This is the content of the test post.',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function guests_cannot_create_posts()
    {
        $postData = [
            'title' => 'Test Post Title',
            'content' => 'This is the content of the test post.'
        ];
        
        $response = $this->post('/posts', $postData);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function post_creation_requires_valid_data()
    {
        $this->actingAs($this->user);
        
        $response = $this->post('/posts', []);
        $response->assertSessionHasErrors(['title', 'content']);
    }

    /** @test */
    public function post_owners_can_edit_their_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        
        $this->actingAs($this->user);
        
        $response = $this->get("/posts/{$post->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('posts.edit');
    }

    /** @test */
    public function users_cannot_edit_other_users_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
        
        $this->actingAs($this->user);
        
        $response = $this->get("/posts/{$post->id}/edit");
        $response->assertStatus(403);
    }

    /** @test */
    public function admins_can_edit_any_post()
    {
        $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
        
        $this->actingAs($this->admin);
        
        $response = $this->get("/posts/{$post->id}/edit");
        $response->assertStatus(200);
    }

    /** @test */
    public function post_owners_can_update_their_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        
        $this->actingAs($this->user);
        
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content.'
        ];
        
        $response = $this->put("/posts/{$post->id}", $updateData);
        $response->assertRedirect("/posts/{$post->id}");
        
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content.'
        ]);
    }

    /** @test */
    public function users_cannot_update_other_users_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
        
        $this->actingAs($this->user);
        
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content.'
        ];
        
        $response = $this->put("/posts/{$post->id}", $updateData);
        $response->assertStatus(403);
    }

    /** @test */
    public function post_owners_can_delete_their_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        
        $this->actingAs($this->user);
        
        $response = $this->delete("/posts/{$post->id}");
        $response->assertRedirect('/posts');
        
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /** @test */
    public function users_cannot_delete_other_users_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
        
        $this->actingAs($this->user);
        
        $response = $this->delete("/posts/{$post->id}");
        $response->assertStatus(403);
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
    public function posts_are_paginated()
    {
        // Create 15 posts
        Post::factory()->count(15)->create();
        
        $response = $this->get('/posts');
        $response->assertStatus(200);
        
        // Check that pagination is working (Laravel uses different pagination classes)
        $response->assertSee('Previous');
    }

    /** @test */
    public function posts_show_author_and_creation_date()
    {
        $post = Post::factory()->create();
        
        $response = $this->get("/posts/{$post->id}");
        $response->assertSee($post->user->name);
        $response->assertSee($post->created_at->format('M d, Y'));
    }
}