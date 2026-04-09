<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HskSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'exam_date',
        'deadline',
        'is_active',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'deadline' => 'date',
        'is_active' => 'boolean',
    ];
}
