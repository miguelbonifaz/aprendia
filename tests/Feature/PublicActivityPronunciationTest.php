<?php

namespace Tests\Feature;

use App\Models\Activity;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class PublicActivityPronunciationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_generates_and_reuses_pronunciation_for_an_existing_visual_activity(): void
    {
        config([
            'activity_agent.openai.api_key' => 'test-key',
            'activity_agent.openai.speech_voice' => 'cedar',
            'activity_agent.openai.speech_format' => 'wav',
            'activity_agent.openai.speech_mime_type' => 'audio/wav',
        ]);
        Http::preventStrayRequests();
        Http::fake(['api.openai.com/v1/audio/speech' => Http::response(
            'wav-content',
            200,
            ['Content-Type' => 'audio/wav'],
        )]);
        $activity = Activity::factory()->create(['definition' => $this->definition()]);
        $activity->mediaAssets()->create([
            'media_id' => 'question_doll_pronunciation',
            'mime_type' => 'audio/mpeg',
            'content' => base64_encode('old-nova-audio'),
        ]);
        $route = route('activities.pronunciation.show', [
            'activity' => $activity,
            'itemId' => 'question_doll',
        ]);

        $this->get($route)
            ->assertOk()
            ->assertHeader('Content-Type', 'audio/wav')
            ->assertContent('wav-content');
        $this->get($route)->assertOk()->assertContent('wav-content');

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => $request['input'] === 'Muñeca');
        $media = $activity->refresh()->mediaAssets;
        $this->assertCount(2, $media);
        $this->assertNotNull($media->first(fn ($asset): bool => str_starts_with($asset->media_id, 'pronunciation_')));
    }

    /** @return array<string, mixed> */
    private function definition(): array
    {
        $definition = Activity::factory()->make()->definition;
        $definition['items'][0]['id'] = 'question_doll';
        $definition['items'][0]['data']['illustration_media_id'] = 'question_doll_image';
        $definition['items'][0]['data']['feedback']['correct'] = '¡Muy bien! «Muñeca» empieza con «mu».';
        $definition['media'] = [[
            'id' => 'question_doll_image',
            'type' => 'image',
            'source' => 'generated://question_doll_image',
            'alt_text' => 'Una muñeca infantil.',
            'transcript' => null,
        ]];
        $definition['answer_key'] = ['question_doll' => ['option_casa']];
        $definition['hints'] = ['question_doll' => ['Escucha la palabra lentamente.']];

        return $definition;
    }
}
