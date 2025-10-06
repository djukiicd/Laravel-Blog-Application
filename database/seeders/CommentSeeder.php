<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $users = User::all();

        if ($posts->isEmpty() || $users->isEmpty()) {
            $this->command->error('No posts or users found. Please run UserSeeder and PostSeeder first.');
            return;
        }

        $sampleComments = [
            'Great post! Thanks for sharing this information.',
            'I found this very helpful. Looking forward to more content like this.',
            'Interesting perspective. I have a different view on this topic though.',
            'This is exactly what I was looking for. Thank you!',
            'Could you elaborate more on the third point?',
            'I\'ve been struggling with this issue for weeks. Your solution worked perfectly!',
            'Very well written and easy to understand.',
            'I disagree with some points, but overall good content.',
            'This helped me understand the concept much better.',
            'Thanks for the detailed explanation. Much appreciated!',
            'I\'ve bookmarked this for future reference.',
            'Great examples and clear explanations.',
            'This is a game-changer for me. Thank you!',
            'I\'ve been following your blog for a while. Keep up the good work!',
            'Very insightful. I learned something new today.',
            'This is my first time reading about this topic. Great introduction!',
            'I\'ve implemented this in my project and it works great.',
            'Could you recommend any additional resources on this subject?',
            'I\'m new to this field. This post was very beginner-friendly.',
            'Excellent work! Looking forward to the next post.',
        ];

        // Add comments to posts
        foreach ($posts as $post) {
            $commentCount = rand(2, 8); // Random number of comments per post
            
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                    'comment' => $sampleComments[array_rand($sampleComments)],
                    'created_at' => $post->created_at->addDays(rand(1, 10)),
                    'updated_at' => $post->created_at->addDays(rand(1, 10)),
                ]);
            }
        }
    }
}
