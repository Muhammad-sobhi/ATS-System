<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'uploaded_by',
        'filename',
        'url',
        'content_type',
        'size',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // ---- Relationships ----
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
