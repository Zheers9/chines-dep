<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamsQuestion extends Model
{
    protected $fillable = [
        'roadmap_step_id',
        'section_id',
        'type',
        'content',
        'weight',
        'order',
        'audio_url'
    ];

    public function roadmapStep()
    {
        return $this->belongsTo(ExamsRoadmapStep::class, 'roadmap_step_id');
    }

    public function section()
    {
        return $this->belongsTo(ExamsSection::class, 'section_id');
    }


    public function options()
    {
        return $this->hasMany(ExamsQuestionOption::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamsQuestionAnswer::class, 'question_id');
    }

    protected $appends = ['audio_file_url'];

    public function getAudioFileUrlAttribute()
    {
        return $this->audio_url ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->audio_url) : null;
    }
}
