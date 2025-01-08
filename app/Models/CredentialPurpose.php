<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CredentialPurpose extends Model
{
    use HasFactory;

    protected $table = 'credential_purposes';

    protected $fillable = [
        'purpose_id',
        'reqcred_id',
        'copy',
    ];

    public function request_credential(): BelongsTo
    {
        return $this->belongsTo(RequestCredential::class, 'reqcred_id');
    }

    public function purpose(): BelongsTo
    {
        return $this->belongsTo(Purpose::class, 'purpose_id');
    }
}
