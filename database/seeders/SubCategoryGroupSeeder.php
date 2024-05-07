<?php

namespace Database\Seeders;

use App\Models\CategoryGroup;
use App\Models\SubCategoryGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCategoryGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scg = SubCategoryGroup::create([
            'name' => 'Sell an item, property or service',
            'category_group_id' => 1,
        ]);
        
        DB::table('category_sub_category_group')->insert([
            ['sub_category_group_id' => 1, 'category_id' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],     // Vehicles
            ['sub_category_group_id' => 1, 'category_id' => 137, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],   // Other
            ['sub_category_group_id' => 1, 'category_id' => 135, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],   // Agriculture
            ['sub_category_group_id' => 1, 'category_id' => 122, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],   // Essential
            ['sub_category_group_id' => 1, 'category_id' => 114, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],   // Education
            ['sub_category_group_id' => 1, 'category_id' => 97, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Fashion & Beauty
            ['sub_category_group_id' => 1, 'category_id' => 9, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],     // Property
            ['sub_category_group_id' => 1, 'category_id' => 62, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Hobby, Sports & Kids
            ['sub_category_group_id' => 1, 'category_id' => 54, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Animals
            ['sub_category_group_id' => 1, 'category_id' => 46, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Business & Industry
            ['sub_category_group_id' => 1, 'category_id' => 14, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Electronics
            ['sub_category_group_id' => 1, 'category_id' => 37, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Services
            ['sub_category_group_id' => 1, 'category_id' => 30, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Home & Garden
        ]);


        $scg = SubCategoryGroup::create([
            'name' => 'Offer a property for rent',
            'category_group_id' => 1,
        ]);

        DB::table('category_sub_category_group')->insert([
            ['sub_category_group_id' => 2, 'category_id' => 9, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],     // Property
        ]);


        $scg = SubCategoryGroup::create([
            'name' => 'Post a job in Sri Lanka',
            'category_group_id' => 2,
        ]);

        DB::table('category_sub_category_group')->insert([
            ['sub_category_group_id' => 3, 'category_id' => 73, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Jobs
        ]);


        $scg = SubCategoryGroup::create([
            'name' => 'Post a job overseas',
            'category_group_id' => 2,
        ]);

        DB::table('category_sub_category_group')->insert([
            ['sub_category_group_id' => 4, 'category_id' => 136, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],   // Work Overseas
        ]);


        $scg = SubCategoryGroup::create([
            'name' => 'Look for property to rent',
            'category_group_id' => 3,
        ]);

        DB::table('category_sub_category_group')->insert([
            ['sub_category_group_id' => 5, 'category_id' => 9, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],     // Property
        ]);


        $scg = SubCategoryGroup::create([
            'name' => 'Look for something to buy',
            'category_group_id' => 3,
        ]);

        DB::table('category_sub_category_group')->insert([
            ['sub_category_group_id' => 6, 'category_id' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],     // Vehicles
            ['sub_category_group_id' => 6, 'category_id' => 137, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],   // Other
            ['sub_category_group_id' => 6, 'category_id' => 135, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],   // Agriculture
            ['sub_category_group_id' => 6, 'category_id' => 122, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],   // Essential
            ['sub_category_group_id' => 6, 'category_id' => 114, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],   // Education
            ['sub_category_group_id' => 6, 'category_id' => 97, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Fashion & Beauty
            ['sub_category_group_id' => 6, 'category_id' => 9, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],     // Property
            ['sub_category_group_id' => 6, 'category_id' => 62, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Hobby, Sports & Kids
            ['sub_category_group_id' => 6, 'category_id' => 54, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Animals
            ['sub_category_group_id' => 6, 'category_id' => 46, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Business & Industry
            ['sub_category_group_id' => 6, 'category_id' => 14, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Electronics
            ['sub_category_group_id' => 6, 'category_id' => 37, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Services
            ['sub_category_group_id' => 6, 'category_id' => 30, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),],    // Home & Garden
        ]);

    }
}
