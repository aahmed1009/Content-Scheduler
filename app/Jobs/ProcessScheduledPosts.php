<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessScheduledPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $posts = Post::where('status', 'scheduled')
            ->where('scheduled_time', '<=', now())
            ->get();

        foreach ($posts as $post) {
            // Simulate publishing
            $post->update(['status' => 'published']);

            Log::info("Post ID {$post->id} published successfully.");
        }
    }
}