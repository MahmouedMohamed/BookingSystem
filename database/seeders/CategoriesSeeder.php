<?php

namespace Database\Seeders;

use App\Modules\Services\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $fixedCategories = [
            'Hair & Beauty',
            'Health & Wellness',
            'Fitness',
            'Education',
            'Consulting',
        ];

        foreach ($fixedCategories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
    }
}
