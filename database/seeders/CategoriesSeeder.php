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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Items Care', 'Personal Care'];
        foreach ($categories as $key => $category) {
            Category::factory()->create([
                'name' => Str::ucfirst($category),
            ]);
        }
    }
}
