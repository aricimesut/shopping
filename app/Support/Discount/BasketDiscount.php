<?php

namespace App\Support\Discount;

use App\Support\DiscountContract;
use Illuminate\Support\Facades\Log;

class BasketDiscount implements DiscountContract
{
    public function calculate($discount, $order, $total)
    {
        if ($discount->threshold <= $total) {
            $amount = ($total * $discount->discount) / 100;

            return [
                "discountReason" => $discount->reason,
                "discountAmount" => $amount,
                "subtotal" => $total - $amount,
            ];
        }

        return false;

    }
}
