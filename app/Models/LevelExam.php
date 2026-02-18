<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class LevelExam extends Model
{
    use HasFactory;
    protected $fillable = ['type_exam_id', 'level'];

    public function typeExam()
    {
        return $this->belongsTo(TypeExam::class);
    }
    public function registers()
    {
        return $this->hasMany(Register::class);
    }
}
