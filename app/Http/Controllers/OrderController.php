<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends MainController
{
    public function __construct()
    {
        $this->service = new OrderService();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->service->index($request->all());
    }
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function add(Request $request): JsonResponse
    {
        $rules = [
            "customerId" => "required|exists:customers,id",
            "items" => "required|array",
            "items.*.productId" => "required|exists:products,id",
            "items.*.quantity" => "required|numeric|min:1",
            "items.*.unitPrice" => "required|numeric|min:0",
            "items.*.total" => "required|numeric|min:0",
            "total" => "required|numeric|min:0"
        ];

        $validator = Validator::make($request->all(), $rules, [], [
            "customerId" => "Customer",
            "items.*.productId" => "Item Product",
            "items.*.quantity" => "Item Quantity",
            "items.*.unitPrice" => "Item Unit Price",
            "items.*.total" => "Total",
            "total" => "Total"
        ]);

        $validator->validate();

        return $this->service->add($validator->validated());
    }

    /**
     * @param Request $request
     * @param $order
     * @return JsonResponse
     */
    public function destroy(Request $request, $order): JsonResponse
    {
        return $this->service->destroy($order);
    }
}
