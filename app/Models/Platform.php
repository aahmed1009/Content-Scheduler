<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type'
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_platforms')
                    ->withPivot('platform_status')
                    ->withTimestamps();
    }
public static function characterLimits()
{
    return [
        'twitter' => 280,
        'linkedin' => 1300,
        'instagram' => 2200,
    ];
}
}