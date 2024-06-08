<?php

namespace App\Support\Discount;

use App\Support\DiscountContract;

class CheapestDiscount implements DiscountContract
{
    public function calculate($discount, $order, $total)
    {
        $discountCategory = $discount->category_id;

        //search in items object for the $discountCategory
        $totalSum = 0;
        foreach ($order->items as $item) {
            if ($item->product->category_id == $discountCategory) {
                $totalSum += $item->quantity;
            }
        }

        if ($totalSum >= $discount->threshold) {
            //find the cheapest item in the order
            $cheapestItem = $order->items->min('total');

            $amount = ($cheapestItem * $discount->discount) / 100;
            return [
                "discountReason" => $discount->reason,
                "discountAmount" => $amount,
                "subtotal" => $total - $amount,
            ];
        }

        return false;

    }
}
