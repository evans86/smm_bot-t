<?php

namespace App\Models\Bot;

use App\Traits\HasSecrets;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Bot extends Model
{
    use HasFactory;
    use HasSecrets;

    protected $guarded = false;
    protected $table = 'bot';

    public function setApiKeyAttribute($value)
    {
        $this->setSecretAttribute($value);
    }

    public function getApiKeyAttribute($value)
    {
        return $this->getSecretAttribute($value);
    }
}
