<?php

namespace App\Http\Resources\api;

use App\Models\Order\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => (integer)$this->order_id,
            'link' => $this->link,
            'start_count' => $this->start_count,
            'remains' => $this->remains,
            'type_name' => $this->type_name,
            'type' => $this->type,
            'cost' => $this->price,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * @param Order $order
     * @return array
     */
    public static function generateOrderArray(Order $order): array
    {
        return [
            'id' => (integer)$order->order_id,
            'link' => $order->link,
            'start_count' => $order->start_count,
            'remains' => $order->remains,
            'type_name' => $order->type_name,
            'type' => $order->type,
            'cost' => $order->price,
            'status' => $order->status,
            'created_at' => $order->created_at,
        ];
    }









}
