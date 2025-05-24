<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image_url',
        'scheduled_time',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'post_platforms')
                    ->withPivot('platform_status')
                    ->withTimestamps();
    }

    public function postPlatforms()
    {
        return $this->hasMany(PostPlatform::class);
    }
    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'image_url' => 'nullable|url',
        'scheduled_time' => 'required|date|after:now',
        'status' => 'required|in:draft,scheduled,published',
        'platform_ids' => 'required|array|min:1',
        'platform_ids.*' => 'exists:platforms,id',
    ]);


    $platformTypes = \App\Models\Platform::whereIn('id', $validated['platform_ids'])->pluck('type')->toArray();


    $limits = Platform::characterLimits();

    foreach ($platformTypes as $type) {
        if (isset($limits[$type]) && strlen($validated['content']) > $limits[$type]) {
            return response()->json([
                'error' => "Content exceeds the character limit for {$type} ({$limits[$type]} characters max)."
            ], 422);
        }
    }


    $post = auth()->user()->posts()->create($validated);
    $post->platforms()->attach($validated['platform_ids']);

    return response()->json($post->load('platforms'), 201);
}

}