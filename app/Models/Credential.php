<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Credential extends Model
{
    use HasFactory;

    protected $table = 'credentials';

    protected $fillable = [
        'credential_name',
        'amount',
        'on_page',
        'count_day',
    ];

    public function request_credential(): HasOne
    {
        return $this->hasOne(RequestCredential::class, 'credential_id');
    }
}
