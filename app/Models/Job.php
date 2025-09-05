<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'recruiter_id',
        'title',
        'slug',
        'description',
        'location',
        'type',
        'department',
        'slots',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // ---- ENUM constants ----
    const TYPE_FULL_TIME = 'full_time';
    const TYPE_PART_TIME = 'part_time';
    const TYPE_REMOTE    = 'remote';
    const TYPE_CONTRACT  = 'contract';

    const STATUS_OPEN   = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_PAUSED = 'paused';

    public static function types(): array
    {
        return [
            self::TYPE_FULL_TIME,
            self::TYPE_PART_TIME,
            self::TYPE_REMOTE,
            self::TYPE_CONTRACT,
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_CLOSED,
            self::STATUS_PAUSED,
        ];
    }

    // ---- Relationships ----
    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'job_tags');
    }

    public function stages()
    {
        return $this->hasMany(JobStage::class)->orderBy('order');
    }
}
