<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamsUserProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'roadmap_step_id',
        'score',
        'status',
        'completed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function step()
    {
        return $this->belongsTo(ExamsRoadmapStep::class, 'roadmap_step_id');
    }
}
