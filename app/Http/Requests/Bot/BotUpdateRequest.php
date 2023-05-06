<?php

namespace App\Http\Requests\Bot;

use App\Dto\BotDto;
use App\Helpers\ApiHelpers;
use App\Services\Activate\BotService;
use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;


class BotUpdateRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules()
    {
        return [
            'id' => 'required|integer',
            'public_key' => 'required|string',
            'private_key' => 'required|string',
            'version' => 'required|string|min:1|max:1',
            'category_id' => 'required|integer|min:1',
            'percent' => 'required|integer|min:0',
            'api_key' => 'required|string',
            'resource_link' => 'string|nullable',
        ];
    }

    /**
     * @return BotDto
     */
    public function getDto(): BotDto
    {
        $dto = new BotDto();
        $dto->id = intval($this->id);
        $dto->public_key = $this->public_key;
        $dto->private_key = $this->private_key;
        $dto->bot_id = intval($this->bot_id);
        $dto->api_key = $this->api_key;
        $dto->category_id = intval($this->category_id);
        $dto->percent = intval($this->percent);
        $dto->version = intval($this->version);
        if(filter_var($this->resource_link, FILTER_VALIDATE_URL) === false)
            $dto->resource_link = BotService::DEFAULT_HOST;
        else
            $dto->resource_link = $this->resource_link;
        return $dto;
    }

    /**
     * @inheritDoc
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()
            ->make(ApiHelpers::error($validator->errors()->first()), 422);

        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

}
