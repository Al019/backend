<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestCredential extends Model
{
    use HasFactory;

    protected $table = 'request_credentials';

    protected $fillable = [
        'credential_id',
        'request_id',
        'price',
        'page',
        'reqcred_status',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function credential(): BelongsTo
    {
        return $this->belongsTo(Credential::class, 'credential_id');
    }

    public function credential_purpose(): HasMany
    {
        return $this->hasMany(CredentialPurpose::class, 'reqcred_id');
    }
}
