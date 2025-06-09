<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Tag;

class PostSeeder extends Seeder
{
    /**
     * The array of posts to be seeded
     */
    protected array $posts = [];

    /**
     * Generate random post data
     */
    private function generateRandomPost($id): array
    {
        $titles = [
            'Exploring the Future of Technology',
            'Understanding Modern Web Development',
            'The Rise of Artificial Intelligence',
            'Blockchain Technology Explained',
            'Cloud Computing Best Practices',
            'Cybersecurity Essentials',
            'Data Science Fundamentals',
            'Mobile App Development Trends',
            'DevOps and Continuous Integration',
            'The Internet of Things Revolution',
            'Machine Learning Applications',
            'Software Architecture Patterns',
            'User Experience Design Principles',
            'Agile Development Methodology',
            'Database Optimization Techniques'
        ];

        $bodies = [
            "In today's rapidly evolving digital landscape, understanding the core principles of technology is more important than ever. This article explores the fundamental concepts that drive innovation and progress in our modern world.",
            "The field of web development continues to transform at an unprecedented pace. From static pages to dynamic applications, the journey of web technologies has been nothing short of remarkable.",
            "Artificial Intelligence is reshaping industries across the globe. This comprehensive guide examines how AI is being implemented in various sectors and what the future might hold.",
            "Blockchain technology has emerged as a revolutionary force in digital transactions. Learn about its underlying principles and potential applications in different industries.",
            "Cloud computing has become an essential component of modern business operations. Discover the best practices for implementing and managing cloud infrastructure effectively."
        ];

        $users = range(1, 12);
        // Get valid category IDs from the database
        $categories = DB::table('categories')->pluck('id')->toArray();
        if (empty($categories)) {
            $categories = [1]; // Fallback to category 1 if none exist
        }

        // Generate 0-3 random links
        $links = [];
        $numLinks = rand(0, 3);
        for ($i = 0; $i < $numLinks; $i++) {
            $links[] = [
                'link_id' => rand(1, 15),
                'type' => ['relates_to', 'next', 'parent_for', 'duplicates'][rand(0, 3)],
                'link_title' => $titles[array_rand($titles)]
            ];
        }

        return [
            'id' => $id,
            'title' => $titles[array_rand($titles)] . ' - ' . Str::random(5),
            'body' => $bodies[array_rand($bodies)],
            'json' => json_encode([]),
            'links' => json_encode($links),
            'user_id' => $users[array_rand($users)],
            'category_id' => $categories[array_rand($categories)],
            'created_at' => now()->subDays(rand(1, 365))->format('Y-m-d H:i:s'),
            'updated_at' => now()->subDays(rand(0, 30))->format('Y-m-d H:i:s'),
            'is_published' => rand(0, 1),
            'is_public' => rand(0, 1),
            'is_featured' => rand(0, 1),
            'is_locked' => rand(0, 1),
            'is_self' => rand(0, 1),
            'is_smart' => rand(0, 1),
            'is_active' => 1,
            'is_archived' => 0,
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing user IDs
        $userIds = DB::table('users')->pluck('id')->toArray();
        if (empty($userIds)) {
            throw new \Exception('No users found in the database. Please run UserSeeder first.');
        }

        // Get all existing category IDs
        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        if (empty($categoryIds)) {
            throw new \Exception('No categories found in the database. Please run CategorySeeder first.');
        }

        // Get all existing post type IDs
        $postTypeIds = DB::table('post_types')->pluck('id')->toArray();
        if (empty($postTypeIds)) {
            throw new \Exception('No post types found in the database. Please run PostTypeSeeder first.');
        }

        // Generate 300 random posts
        $randomPosts = [];
        for ($i = 0; $i < 300; $i++) {
            $post = $this->generateRandomPost(count($randomPosts) + 1);
            
            // Ensure user_id exists
            $post['user_id'] = $userIds[array_rand($userIds)];
            
            // Ensure category_id exists
            $post['category_id'] = $categoryIds[array_rand($categoryIds)];
            
            // Ensure post_type_id exists
            $post['post_type_id'] = $postTypeIds[array_rand($postTypeIds)];
            
            // Ensure slug is unique
            if (empty($post['slug'])) {
                $post['slug'] = Str::slug($post['title']) . '-' . $post['id'] . '-' . time();
            }
            
            $randomPosts[] = $post;
        }

        // Add the random posts to the existing posts array
        $this->posts = array_merge($this->posts, $randomPosts);

        // Use a transaction to ensure data consistency
        DB::beginTransaction();
        try {
            foreach ($this->posts as $post) {
                $post = Post::updateOrCreate(
                    ['id' => $post['id']],
                    $post
                );
                $tags = Tag::inRandomOrder()->take(2)->get();
                $post->tags()->attach($tags);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
