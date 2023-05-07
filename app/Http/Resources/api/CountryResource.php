<?php

namespace App\Http\Resources\api;

use App\Models\Country\Country;
use App\Models\User\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'id' => $this->org_id,
            'title_ru' => $this->name_ru,
            'title_eng' => $this->name_en,
            'image' => $this->image,
        ];
    }

    /**
     * @param User $user
     * @param Country $country
     * @return array
     */
    public static function generateUserArray(User $user, Country $country): array
    {
        return [
            'id' => $user->telegram_id,
            'country' => $country->org_id,
            'language' => $user->language,
            'service' => $user->service
        ];
    }
}
