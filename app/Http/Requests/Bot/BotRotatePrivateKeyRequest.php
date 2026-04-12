<?php

namespace App\Http\Requests\Bot;

use App\Helpers\ApiHelpers;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BotRotatePrivateKeyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'public_key' => 'required|string',
            'private_key' => 'required|string',
            'new_private_key' => [
                'required',
                'string',
                'different:private_key',
                Rule::unique('bot', 'private_key'),
            ],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $response = response()
            ->make(ApiHelpers::error($validator->errors()->first()), 422);

        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
