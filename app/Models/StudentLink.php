<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLink extends Model
{
    use HasFactory;

    protected $table = 'student_links';

    protected $fillable = [
        'credential_id',
        'student_id',
    ];
}
