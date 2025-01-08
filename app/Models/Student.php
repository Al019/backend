<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'info_id',
        'id_number',
        'course',
        'student_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function information(): BelongsTo
    {
        return $this->belongsTo(Information::class, 'info_id');
    }

    public function submit(): HasOne
    {
        return $this->hasOne(Submit::class, 'student_id');
    }
}
