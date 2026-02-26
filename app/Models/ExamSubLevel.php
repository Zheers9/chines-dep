<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubLevel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'exam_sub_type_id'];

    public function examSubType()
    {
        return $this->belongsTo(ExamSubType::class);
    }
}
