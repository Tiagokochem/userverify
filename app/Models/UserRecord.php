<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRecord extends Model
{
    protected $fillable = [
        'cpf',
        'cep',
        'email',
        'external_data',
        'risk_level'
    ];

    protected $casts = [
        'external_data' => 'array'
    ];
}
