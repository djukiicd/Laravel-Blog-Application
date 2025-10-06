<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->otherUser = User::factory()->create(['role' => 'user']);
        $this->post = Post::factory()->create();
    }

    /** @test */
    public function guests_can_view_comments_on_posts()
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id]);
        
        $response = $this->get("/posts/{$this->post->id}");
        $response->assertStatus(200);
        $response->assertSee($comment->comment);
    }

    /** @test */
    public function authenticated_users_can_create_comments()
    {
        $this->actingAs($this->user);
        
        $commentData = [
            'comment' => 'This is a test comment.'
        ];
        
        $response = $this->post("/posts/{$this->post->id}/comments", $commentData);
        $response->assertRedirect("/posts/{$this->post->id}");
        
        $this->assertDatabaseHas('comments', [
            'comment' => 'This is a test comment.',
            'post_id' => $this->post->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function guests_cannot_create_comments()
    {
        $commentData = [
            'comment' => 'This is a test comment.'
        ];
        
        $response = $this->post("/posts/{$this->post->id}/comments", $commentData);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function comment_creation_requires_valid_data()
    {
        $this->actingAs($this->user);
        
        $response = $this->post("/posts/{$this->post->id}/comments", []);
        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function comment_creation_has_maximum_length_validation()
    {
        $this->actingAs($this->user);
        
        $longComment = str_repeat('a', 1001); // Exceeds 1000 character limit
        
        $response = $this->post("/posts/{$this->post->id}/comments", [
            'comment' => $longComment
        ]);
        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function comment_owners_can_delete_their_comments()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'post_id' => $this->post->id
        ]);
        
        $this->actingAs($this->user);
        
        $response = $this->delete("/comments/{$comment->id}");
        $response->assertRedirect("/posts/{$this->post->id}");
        
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function post_owners_can_delete_comments_on_their_posts()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_id' => $this->post->id
        ]);
        
        $this->actingAs($this->post->user);
        
        $response = $this->delete("/comments/{$comment->id}");
        $response->assertRedirect("/posts/{$this->post->id}");
        
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function admins_can_delete_any_comment()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_id' => $this->post->id
        ]);
        
        $this->actingAs($this->admin);
        
        $response = $this->delete("/comments/{$comment->id}");
        $response->assertRedirect("/posts/{$this->post->id}");
        
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function users_cannot_delete_other_users_comments()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->otherUser->id,
            'post_id' => $this->post->id
        ]);
        
        $this->actingAs($this->user);
        
        $response = $this->delete("/comments/{$comment->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function comments_show_author_and_creation_date()
    {
        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id
        ]);
        
        $response = $this->get("/posts/{$this->post->id}");
        $response->assertSee($comment->user->name);
        $response->assertSee($comment->created_at->format('M d, Y'));
    }

    /** @test */
    public function comments_are_displayed_in_chronological_order()
    {
        $firstComment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'created_at' => now()->subHour()
        ]);
        
        $secondComment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'created_at' => now()
        ]);
        
        $response = $this->get("/posts/{$this->post->id}");
        $response->assertStatus(200);
        
        // Check that comments are displayed in order
        $content = $response->getContent();
        $firstPosition = strpos($content, $firstComment->comment);
        $secondPosition = strpos($content, $secondComment->comment);
        
        $this->assertTrue($firstPosition < $secondPosition);
    }

    /** @test */
    public function deleting_post_deletes_associated_comments()
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id]);
        
        $this->actingAs($this->post->user);
        
        $response = $this->delete("/posts/{$this->post->id}");
        $response->assertRedirect('/posts');
        
        $this->assertDatabaseMissing('posts', ['id' => $this->post->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}