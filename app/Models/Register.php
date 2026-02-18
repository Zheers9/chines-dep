<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Register extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'level_exam_id',
        'paid_status',
        'image',
    ];

    protected $casts = [
        'paid_status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function levelExam()
    {
        return $this->belongsTo(LevelExam::class);
    }
}