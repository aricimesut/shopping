<?php

namespace App\Http\Controllers;

use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscountController extends MainController
{
    public function __construct()
    {
        $this->service = new DiscountService();
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
     */
    public function index(Request $request, $orderId): JsonResponse
    {
        return $this->service->index($orderId);
    }

}
