<?php

namespace App\Support;

interface DiscountContract
{
    public function calculate($discount, $order, $total);
}
