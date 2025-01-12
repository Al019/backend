<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailVerification extends Model
{
    use HasFactory;

    protected $table = 'email_verifications';

    protected $fillable = [
        'info_id',
        'otp',
        'expired_at',
    ];

    public function information(): BelongsTo
    {
        return $this->belongsTo(Information::class, 'info_id');
    }
}
