<?php

namespace Tests\Unit;

use App\Activities\Agent\OpenAIActivitySpeech;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class OpenAIActivitySpeechTest extends TestCase
{
    public function test_it_generates_a_spanish_pronunciation_with_openai(): void
    {
        config([
            'activity_agent.openai.api_key' => 'test-key',
            'activity_agent.openai.speech_model' => 'gpt-4o-mini-tts',
            'activity_agent.openai.speech_voice' => 'cedar',
            'activity_agent.openai.speech_speed' => 1.0,
        ]);
        Http::preventStrayRequests();
        Http::fake(['api.openai.com/v1/audio/speech' => Http::response(
            'mp3-content',
            200,
            ['Content-Type' => 'audio/mpeg'],
        )]);

        $content = app(OpenAIActivitySpeech::class)->generate('muñeca');

        $this->assertSame('mp3-content', $content);
        Http::assertSent(fn (Request $request): bool => $request['model'] === 'gpt-4o-mini-tts'
            && $request['voice'] === 'cedar'
            && $request['input'] === 'muñeca'
            && $request['response_format'] === 'mp3'
            && $request['speed'] === 1.0);
    }
}
