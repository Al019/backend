<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submit extends Model
{
    use HasFactory;

    protected $table = 'submits';

    protected $fillable = [
        'student_id',
        'submit_message',
        'submit_status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function record(): HasMany
    {
        return $this->hasMany(Record::class, 'submit_id');
    }
}
