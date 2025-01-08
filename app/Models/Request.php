<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'student_id',
        'request_number',
        'request_message',
        'request_status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function request_credential(): HasOne
    {
        return $this->hasOne(RequestCredential::class, 'request_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'request_id');
    }
}
