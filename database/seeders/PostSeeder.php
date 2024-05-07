<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory()
            ->count(1)
            ->create([
                'category_id' => 137,
                'sub_category_group_id' => 1,
                'city_id' => 54,
            ]);
    }
}
