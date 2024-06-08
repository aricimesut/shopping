<?php

namespace App\Support\Discount;

use App\Support\DiscountContract;
use Illuminate\Support\Facades\Log;

class FreeDiscount implements DiscountContract
{
    public function calculate($discount, $order, $total)
    {
        $discountCategory = $discount->category_id;

        $categoryItems = [];
        foreach ($order->items as $item) {
            if ($item->product->category_id == $discountCategory) {
                if (isset($categoryItems[$item->product_id])) {
                    $categoryItems[$item->product_id]['quantity'] += $item->quantity;
                } else {
                    $categoryItems[$item->product_id] = [
                        "quantity" => $item->quantity,
                        "unit_price" => $item->unit_price
                    ];
                }
            }
        }

        $amount = 0;

        foreach ($categoryItems as $item) {
            if ($item['quantity'] >= $discount->threshold) {
                $discountNumber = floor($item['quantity'] / $discount->threshold);
                $amount += $discountNumber * $item['unit_price'] * $discount->discount;
            }
        }

        if ($amount > 0) {
            return [
                "discountReason" => $discount->reason,
                "discountAmount" => $amount,
                "subtotal" => $total - $amount,
            ];
        }

        return false;

    }
}
