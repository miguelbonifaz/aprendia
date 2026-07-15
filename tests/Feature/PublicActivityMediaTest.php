<?php

namespace Tests\Feature;

use App\Models\Activity;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class PublicActivityMediaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_generated_image_is_publicly_available_through_activity_link(): void
    {
        $activity = Activity::factory()->create();
        $activity->mediaAssets()->create([
            'media_id' => 'question_1_image',
            'mime_type' => 'image/webp',
            'content' => base64_encode('webp-image'),
        ]);

        $this->get(route('activities.media.show', [
            'activity' => $activity,
            'mediaId' => 'question_1_image',
        ]))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/webp')
            ->assertHeader('Cache-Control', 'immutable, max-age=31536000, public')
            ->assertContent('webp-image');
    }

    public function test_media_from_another_activity_is_not_exposed(): void
    {
        $activity = Activity::factory()->create();
        Activity::factory()->create()->mediaAssets()->create([
            'media_id' => 'private_image',
            'mime_type' => 'image/webp',
            'content' => base64_encode('private'),
        ]);

        $this->get(route('activities.media.show', [
            'activity' => $activity,
            'mediaId' => 'private_image',
        ]))->assertNotFound();
    }
}
