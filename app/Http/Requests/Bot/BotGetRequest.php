<?php

namespace App\Http\Requests\Bot;

use App\Helpers\ApiHelpers;
use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BotGetRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules()
    {
        return [
            'public_key' => 'required|string',
            'private_key' => 'required|string',
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
