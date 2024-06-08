<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "customerId" => $this->customer_id,
            "items" => OrderItemResource::collection($this->items),
            "total" => $this->total,
        ];
    }
}
