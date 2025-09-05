<?php

namespace Database\Seeders;

use App\Modules\Services\Models\Category;
use App\Modules\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
