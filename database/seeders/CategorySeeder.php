<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Parkir Sembarangan',
                'description' => 'Kendaraan parkir di tempat yang tidak seharusnya',
            ],
            [
                'category_name' => 'Parkir di Trotoar',
                'description' => 'Kendaraan parkir di trotoar yang mengganggu pejalan kaki',
            ],
            [
                'category_name' => 'Parkir Ganda',
                'description' => 'Kendaraan parkir ganda yang menghalangi kendaraan lain',
            ],
            [
                'category_name' => 'Parkir di Zona Larangan',
                'description' => 'Kendaraan parkir di area yang dilarang parkir',
            ],
            [
                'category_name' => 'Parkir di Jalur Darurat',
                'description' => 'Kendaraan parkir di jalur darurat atau fire hydrant',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
