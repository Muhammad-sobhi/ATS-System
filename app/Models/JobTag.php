<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class JobTag extends Pivot
{
    protected $table = 'job_tags';

    protected $fillable = [
        'job_id',
        'tag_id',
    ];
}
