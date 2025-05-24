<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class PostController extends Controller
{
    // Create a new post
    public function store(Request $request)
    {
        // Rate limit check
        if ($request->status === 'scheduled') {
            $count = auth()->user()->posts()
                ->where('status', 'scheduled')
                ->whereDate('scheduled_time', now()->toDateString())
                ->count();

            if ($count >= 10) {
                return response()->json([
                    'error' => 'You have reached the maximum of 10 scheduled posts for today.'
                ], 429);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'scheduled_time' => 'required|date|after:now',
            'status' => 'required|in:draft,scheduled,published',
            'platform_ids' => 'required|array|min:1',
            'platform_ids.*' => 'exists:platforms,id',
        ]);

        // Character limit validation
        try {
            $platformTypes = Platform::whereIn('id', $validated['platform_ids'])->pluck('type')->toArray();
            $limits = Platform::characterLimits();

            foreach ($platformTypes as $type) {
                if (isset($limits[$type]) && strlen($validated['content']) > $limits[$type]) {
                    return response()->json([
                        'error' => "Content exceeds the character limit for {$type} ({$limits[$type]} characters max)."
                    ], 422);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Character limit validation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Validation error occurred.'], 500);
        }

        $post = Auth::user()->posts()->create($validated);
        $post->platforms()->attach($validated['platform_ids']);

        // Log activity
        ActivityLogger::log('post_created', "Post ID {$post->id} was created.");

        return response()->json($post->load('platforms'), 201);
    }

    // Get all posts with filters
    public function index(Request $request)
    {
        $query = Auth::user()->posts()->with('platforms');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('scheduled_time', $request->date);
        }

        return $query->latest()->get();
    }

    // Update a post
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'image_url' => 'nullable|url',
            'scheduled_time' => 'sometimes|required|date|after:now',
            'status' => 'in:draft,scheduled,published',
            'platform_ids' => 'sometimes|array|min:1',
            'platform_ids.*' => 'exists:platforms,id',
        ]);

        $post->update($validated);

        if (isset($validated['platform_ids'])) {
            $post->platforms()->sync($validated['platform_ids']);
        }

        // Log activity
        ActivityLogger::log('post_updated', "Post ID {$post->id} was updated.");

        return response()->json($post->load('platforms'));
    }

    // Delete a post
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->delete();

        // Log activity
        ActivityLogger::log('post_deleted', "Post ID {$post->id} was deleted.");

        return response()->json(['message' => 'Post deleted']);
    }
}