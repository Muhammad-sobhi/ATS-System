<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationActivity extends Model
{
    use HasFactory;

    public $timestamps = false; // because migration only has created_at

    protected $fillable = [
        'application_id',
        'actor_id',
        'type',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    // ---- ENUM constants ----
    const TYPE_STATUS_CHANGE = 'status_change';
    const TYPE_COMMENT       = 'comment';
    const TYPE_ASSIGNMENT    = 'assignment';
    const TYPE_FILE_UPLOAD   = 'file_upload';
    const TYPE_NOTE          = 'note';

    public static function types(): array
    {
        return [
            self::TYPE_STATUS_CHANGE,
            self::TYPE_COMMENT,
            self::TYPE_ASSIGNMENT,
            self::TYPE_FILE_UPLOAD,
            self::TYPE_NOTE,
        ];
    }

    // ---- Relationships ----
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
