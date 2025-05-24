<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Platform;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

       
        \App\Models\Platform::factory()->create([
            'id' => 1,
            'name' => 'Twitter',
            'type' => 'twitter',
        ]);
    }


    public function test_authenticated_user_can_create_a_post()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts', [
            'title' => 'Test Post',
            'content' => 'This is a valid test post.',
            'image_url' => 'https://example.com/image.png',
            'scheduled_time' => now()->addHour()->toDateTimeString(),
            'status' => 'scheduled',
            'platform_ids' => [1]
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Test Post']);
    }

    public function test_post_content_fails_character_limit_for_twitter()
    {
        $user = User::factory()->create();
        $longContent = str_repeat('a', 300); // exceeds 280 char

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts', [
            'title' => 'Too Long',
            'content' => $longContent,
            'image_url' => 'https://example.com/img.png',
            'scheduled_time' => now()->addHour()->toDateTimeString(),
            'status' => 'scheduled',
            'platform_ids' => [1]
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'Content exceeds the character limit for twitter (280 characters max).']);
    }

    public function test_user_cannot_schedule_more_than_10_posts_per_day()
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            $user->posts()->create([
                'title' => "Post $i",
                'content' => "Some content",
                'scheduled_time' => now(),
                'status' => 'scheduled'
            ])->platforms()->attach([1]);
        }

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts', [
            'title' => 'Limit Exceeded',
            'content' => 'Another post',
            'scheduled_time' => now()->addMinute()->toDateTimeString(),
            'status' => 'scheduled',
            'platform_ids' => [1]
        ]);

        $response->assertStatus(429)
                 ->assertJsonFragment(['error' => 'You have reached the maximum of 10 scheduled posts for today.']);
    }
}