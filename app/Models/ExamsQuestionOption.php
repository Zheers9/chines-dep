<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamsQuestionOption extends Model
{
    protected $fillable = ['question_id', 'option_text', 'is_correct'];
    protected $casts = ['is_correct' => 'boolean'];
}

/* --- Separate File: ExamsQuestionAnswer --- */
// (I will create these in separate calls)
