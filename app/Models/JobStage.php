<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'name',
        'order',
        'is_default',
    ];

    // ---- Relationships ----
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'stage_id');
    }
}
