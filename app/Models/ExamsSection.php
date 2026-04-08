<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamsSection extends Model
{
    protected $fillable = [
        'roadmap_step_id',
        'title',
        'type',
        'marks',
        'passage',
        'order'
    ];

    public function roadmapStep()
    {
        return $this->belongsTo(ExamsRoadmapStep::class, 'roadmap_step_id');
    }

    public function questions()
    {
        return $this->hasMany(ExamsQuestion::class, 'section_id')->orderBy('order');
    }
}
