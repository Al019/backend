<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'document_name',
        'document_type',
    ];

    public function record(): HasMany
    {
        return $this->hasMany(Record::class, 'document_id');
    }
}
