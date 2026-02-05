<?php

namespace Database\Seeders;

use App\Models\Category;
use DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example categories
        $categories = [
            ['name' => 'Work', 'color' => '#FF5733'],
            ['name' => 'Shopping', 'color' => '#3357FF'],
            ['name' => 'Health', 'color' => '#FF33A8'],
            ['name' => 'Finance', 'color' => '#A833FF'],
        ];
        // Delete the existing records to avoid duplicates
        DB::table('categories')->delete();
        foreach ($categories as $category) {
            /*
            * Merging with user_id == 1 for seeding purposes
            */
            Category::create(array_merge($category, ['user_id' => 1]));
        }
    }
}
