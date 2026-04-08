<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamsRoadmapStep extends Model
{
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
        'color'
    ];

    public function examSubType()
    {
        return $this->belongsTo(ExamSubType::class, 'exam_sub_type_id');
    }

    public function preNode()
    {
        return $this->belongsTo(self::class, 'pre_node_id');
    }

    public function questions()
    {
        return $this->hasMany(ExamsQuestion::class, 'roadmap_step_id')->orderBy('order');
    }

    public function files()
    {
        return $this->hasMany(ExamsRoadmapFile::class, 'roadmap_step_id');
    }

    public function sections()
    {
        return $this->hasMany(ExamsSection::class, 'roadmap_step_id')->orderBy('order');
    }
}
