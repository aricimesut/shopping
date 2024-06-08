<?php

namespace App\Services;

use App\Http\Resources\OrderResource;
use App\Http\Resources\PaginateCollection;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService extends MainService
{
    function __construct()
    {
        $this->model = new Order();
    }

    public function index($data)
    {
        $orders =  $this->model::with("items")->paginate($data["pagination"] ?? 10);

        return $this->response(data: json_encode(new PaginateCollection($orders, OrderResource::class)));
    }

    public function add($data)
    {
        //check for quantities
        foreach ($data["items"] as $item) {
            $product = Product::find($item["productId"]);

            if ($product->stock < $item["quantity"])
                return $this->response("There is no enough quantity in stock for " . $product->name, 422, null);
        }

        //start transaction
        DB::beginTransaction();
        try {
            //insert order
            $order =  $this->model::create([
                "customer_id" => $data["customerId"],
                "total" => $data["total"]
            ]);

            //insert order items
            foreach ($data["items"] as $item) {
                $order->items()->create([
                    "product_id" => $item["productId"],
                    "quantity" => $item["quantity"],
                    "unit_price" => $item["unitPrice"],
                    "total" => $item["total"]
                ]);

                //update stock
                $product = Product::find($item["productId"]);
                $product->stock -= $item["quantity"];
                $product->save();
            }
        } catch (\Exception $e) {
            //rollback transaction
            DB::rollBack();
            return $this->response("Error occurred while adding order", 500, null);
        }

        //commit transaction
        DB::commit();

        return $this->response(data: ["orderId" => $order->id]);
    }

    public function destroy($orderId)
    {
        $order = Order::find($orderId);

        if (!$order)
            return $this->response("Order not found", 404, null);

        //start transaction
        DB::beginTransaction();
        try {
            //delete order items
            foreach ($order->items as $item) {
                //update stock
                $product = Product::find($item->product_id);
                $product->stock += $item->quantity;
                $product->save();

                $item->delete();
            }

            //delete order
            $order->delete();
        } catch (\Exception $e) {
            //rollback transaction
            DB::rollBack();
            return $this->response("Error occurred while deleting order", 500, null);
        }

        //commit transaction
        DB::commit();

        return $this->response();
    }
}
