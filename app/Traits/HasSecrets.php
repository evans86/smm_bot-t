<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait HasSecrets
{
    public function setSecretAttribute($value, $field = 'api_key')
    {
        if (!empty($value) && !str_contains($value, '****')) {
            $this->attributes[$field] = Crypt::encryptString($value);
        }
    }

    public function getSecretAttribute($value, $field = 'api_key')
    {
        if (empty($value)) return '';

        try {
            $decrypted = Crypt::decryptString($value);
            return '****' . substr($decrypted, -4);
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getDecryptedSecret($field = 'api_key'): string
    {
        if (empty($this->attributes[$field])) return '';

        try {
            return Crypt::decryptString($this->attributes[$field]);
        } catch (\Exception $e) {
            return '';
        }
    }
}
