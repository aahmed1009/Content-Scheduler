<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PostPlatform extends Pivot
{
    use HasFactory;

    protected $table = 'post_platforms';

    protected $fillable = [
        'post_id',
        'platform_id',
        'platform_status'
    ];
}
