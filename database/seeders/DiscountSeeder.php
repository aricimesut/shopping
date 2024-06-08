<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleData = [
            [
                "id" => 1,
                "reason" => "10_PERCENT_OVER_1000",
                "threshold" => 1000,
                "discount" => 10,
                "type" => "basket",
            ],
            [
                "id" => 2,
                "reason" => "20_PERCENT_CHEAPEST_IN_CATEGORY_1",
                "category_id" => 1,
                "threshold" => 2,
                "discount" => 20,
                "type" => "cheapest",
            ],
            [
                "id" => 3,
                "reason" => "BUY_5_GET_1",
                "category_id" => 2,
                "threshold" => 5,
                "discount" => 1,
                "type" => "free",
            ]
        ];

        foreach ($sampleData as $data) {
            Discount::create($data);
        }
    }
}
