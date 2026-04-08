<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadmapStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_sub_type_id',
        'pre_node_id',
        'title',
        'description',
        'type',
        'difficulty',
        'video_url',
        'file_path',
        'total_marks',
        'order',
        'color',
    ];

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'roadmap_step_id')->orderBy('order');
    }
}
