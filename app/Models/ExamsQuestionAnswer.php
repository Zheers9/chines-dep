<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamsQuestionAnswer extends Model
{
    protected $fillable = ['question_id', 'answer_text'];
}
