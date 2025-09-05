<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_id',
        'candidate_id',
        'stage_id',
        'applied_at',
        'source',
        'resume_snapshot',
        'cover_letter',
        'assigned_to',
        'notes',
        'meta',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'meta' => 'array',
    ];


    public function moveToStage(JobStage $stage, ?User $recruiter = null, ?string $note = null)
{
    // Update stage
    $this->stage_id = $stage->id;

    // Optionally assign a recruiter
    if ($recruiter) {
        $this->assigned_to = $recruiter->id;
    }

    $this->save();

    // Log activity
    ApplicationActivity::create([
        'application_id' => $this->id,
        'actor_id' => $recruiter?->id,
        'type' => 'status_change',
        'payload' => [
            'new_stage' => $stage->name,
            'note' => $note,
        ],
        'created_at' => now(),
    ]);
}



    // ---- Relationships ----
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function stage()
    {
        return $this->belongsTo(JobStage::class, 'stage_id');
    }

    public function assignedRecruiter()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->hasMany(ApplicationActivity::class);
    }

    public function attachments()
    {
        return $this->hasMany(ApplicationAttachment::class);
    }
}
