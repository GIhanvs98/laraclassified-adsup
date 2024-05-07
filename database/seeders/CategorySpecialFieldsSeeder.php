<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySpecialFieldsSeeder extends Seeder
{
    public function run(): void
    {
        // Land
        Category::find(10)->update([
            "transaction_type" => "both",
        ]);

        // Houses
        Category::find(11)->update([
            "transaction_type" => "both",
        ]);

        // Apartments
        Category::find(12)->update([
            "transaction_type" => "both",
        ]);

        // Commercial Property
        Category::find(1114)->update([
            "transaction_type" => "both",
        ]);

        // Rooms & Annexes
        Category::find(1115)->update([
            "transaction_type" => "rent",
        ]);

        // Holiday & Short-Term Rental
        Category::find(1116)->update([
            "transaction_type" => "rent",
        ]);

    }
}
