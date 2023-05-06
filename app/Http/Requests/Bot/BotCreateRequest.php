<?php

namespace App\Http\Requests\Bot;

use App\Helpers\ApiHelpers;
use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;


class BotCreateRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules()
    {
        return [
            'bot_id' => 'required|integer|unique:bot',
            'public_key' => 'required|string|unique:bot',
            'private_key' => 'required|string|unique:bot',
        ];
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
