<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'roadmap_step_id',
        'type',
        'content',
        'audio_url',
        'weight',
        'order',
    ];

    public function options()
    {
        return $this->hasMany(ExamQuestionOption::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamQuestionAnswer::class, 'question_id');
    }
}
