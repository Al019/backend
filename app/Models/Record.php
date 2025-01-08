<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Record extends Model
{
    use HasFactory;

    protected $table = 'records';

    protected $fillable = [
        'submit_id',
        'document_id',
        'uri',
    ];

    public function submit(): BelongsTo
    {
        return $this->belongsTo(Submit::class, 'submit_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}
