<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        $posts = [
            [
                'title' => 'Welcome to Our Blog!',
                'content' => 'Welcome to our new blog platform! This is our first post where we introduce the features and capabilities of our blog system. We hope you enjoy reading and participating in our community discussions.

Our blog supports user registration, post creation, commenting, and much more. Feel free to explore all the features and create your own posts!',
            ],
            [
                'title' => 'Getting Started with Laravel',
                'content' => 'Laravel is a powerful PHP framework that makes web development enjoyable and creative. In this post, we\'ll explore the basics of Laravel and how it can help you build amazing web applications.

Key features of Laravel include:
- Elegant syntax
- Powerful ORM (Eloquent)
- Built-in authentication
- Blade templating engine
- Artisan command-line tool
- Comprehensive testing support

Whether you\'re a beginner or an experienced developer, Laravel has something to offer for everyone.',
            ],
            [
                'title' => 'The Art of Web Design',
                'content' => 'Web design is more than just making things look pretty. It\'s about creating user experiences that are both functional and beautiful. In today\'s digital world, good web design can make or break a website\'s success.

Here are some principles to keep in mind:
1. User Experience (UX) should always come first
2. Mobile responsiveness is crucial
3. Fast loading times improve user satisfaction
4. Clean and intuitive navigation
5. Consistent branding and visual hierarchy

Remember, great web design is invisible - users shouldn\'t have to think about how to use your website.',
            ],
            [
                'title' => 'Database Design Best Practices',
                'content' => 'Good database design is the foundation of any robust application. Whether you\'re working with MySQL, PostgreSQL, or any other database system, following best practices will save you time and headaches in the long run.

Key principles include:
- Normalize your data appropriately
- Use proper indexing for performance
- Choose appropriate data types
- Plan for scalability from the start
- Implement proper foreign key relationships
- Document your schema decisions

A well-designed database will make your application faster, more reliable, and easier to maintain.',
            ],
            [
                'title' => 'The Future of Web Development',
                'content' => 'Web development is constantly evolving, with new technologies and frameworks emerging regularly. As developers, it\'s important to stay current with trends while also understanding the fundamentals that remain constant.

Current trends include:
- Progressive Web Apps (PWAs)
- Serverless architecture
- Microservices
- AI and machine learning integration
- WebAssembly for performance-critical applications
- JAMstack architecture

The key is to learn continuously while building a solid foundation in core web technologies like HTML, CSS, and JavaScript.',
            ],
        ];

        $tags = Tag::all();

        foreach ($posts as $index => $postData) {
            $post = Post::create([
                'title' => $postData['title'],
                'content' => $postData['content'],
                'user_id' => $users->random()->id,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            // Assign 1-3 random tags to each post
            if ($tags->isNotEmpty()) {
                $randomTags = $tags->random(rand(1, 3));
                $post->tags()->attach($randomTags->pluck('id'));
            }
        }
    }
}
