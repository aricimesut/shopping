<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Order;
use App\Support\DiscountGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DiscountService extends MainService
{
    function __construct()
    {
        $this->model = new Discount();
    }

    public function index($orderId): JsonResponse
    {
        //check if order exists
        $order = Order::with("items.product.category")->find($orderId);

        if (!$order) {
            return $this->response('Order not found', 404);
        }

        //get discounts
        $discounts = $this->model::get();

        $totalDiscounts = [];
        $totalBasket = $order->total;

        foreach ($discounts as $discount) {

            $adapter = (new DiscountGateway(ucfirst($discount->type) . 'Discount'))->getClass();

            $discountResponse = $adapter->calculate($discount, $order, $totalBasket);

            if ($discountResponse) {
                $totalDiscounts[] = $discountResponse;
                $totalBasket = $discountResponse['subtotal'];
            }
        }

        return $this->response('Discounts retrieved', 200, [
            "orderId" => $orderId,
            "discounts" => $totalDiscounts,
            "totalDiscount" => array_sum(array_column($totalDiscounts, 'discountAmount')),
            "discountedTotal" => $totalBasket,
        ]);

    }

}
