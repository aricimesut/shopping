<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "productId" => $this->product_id,
            "quantity" => $this->quantity,
            "unitPrice" => $this->unit_price,
            "total" => $this->total,
        ];
    }
}
