<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'title',
        'certificate',
        'role',
        'description',
        'image',
        'email',
        'phone',
        'display_order',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function gallery()
    {
        return $this->hasMany(StaffGallery::class, 'staff_member_id');
    }
}
