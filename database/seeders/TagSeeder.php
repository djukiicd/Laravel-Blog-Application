<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Laravel', 'description' => 'PHP web application framework'],
            ['name' => 'PHP', 'description' => 'Server-side scripting language'],
            ['name' => 'Web Development', 'description' => 'Building websites and web applications'],
            ['name' => 'Tutorial', 'description' => 'Educational content and guides'],
            ['name' => 'Tips', 'description' => 'Useful tips and tricks'],
            ['name' => 'News', 'description' => 'Latest news and updates'],
            ['name' => 'JavaScript', 'description' => 'Client-side programming language'],
            ['name' => 'CSS', 'description' => 'Styling language for web pages'],
            ['name' => 'HTML', 'description' => 'Markup language for web pages'],
            ['name' => 'Database', 'description' => 'Data storage and management'],
            ['name' => 'API', 'description' => 'Application Programming Interface'],
            ['name' => 'Security', 'description' => 'Web security and best practices'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
