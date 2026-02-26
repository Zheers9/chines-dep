<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSubType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type_exam_id'];

    public function typeExam()
    {
        return $this->belongsTo(TypeExam::class);
    }

    public function examSubLevels()
    {
        return $this->hasMany(ExamSubLevel::class);
    }
}
