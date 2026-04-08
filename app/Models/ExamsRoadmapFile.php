<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExamsRoadmapFile extends Model
{
    protected $fillable = [
        'roadmap_step_id',
        'file_path',
        'original_name',
        'file_type',
        'title',
        'description'
    ];

    protected $appends = ['full_url'];

    public function getFullUrlAttribute()
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    public function roadmapStep()
    {
        return $this->belongsTo(ExamsRoadmapStep::class, 'roadmap_step_id');
    }
}
