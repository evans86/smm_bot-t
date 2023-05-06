<?php

namespace App\Http\Resources\api;

use App\Models\User\SmsUser;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * @param SmsUser $user
     * @return array
     */
    public static function generateUserArray(SmsUser $user): array
    {
        return [
            'id' => $user->telegram_id,
            'language' => $user->language,
            'service' => $user->service
        ];
    }
}
