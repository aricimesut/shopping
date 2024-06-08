<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleData = [
            [
                "id" => 1,
                "name" => "El Aletleri",
            ],
            [
                "id" => 2,
                "name" => "Anahtarlar",
            ]
        ];


        foreach ($sampleData as $data) {
            Category::create($data);
        }
    }
}
