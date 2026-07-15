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
            'activity_agent.openai.speech_format' => 'wav',
            'activity_agent.openai.speech_mime_type' => 'audio/wav',
        ]);
        Http::preventStrayRequests();
        Http::fake(['api.openai.com/v1/audio/speech' => Http::response(
            'wav-content',
            200,
            ['Content-Type' => 'audio/wav'],
        )]);

        $content = app(OpenAIActivitySpeech::class)->generate('muñeca');

        $this->assertSame('wav-content', $content);
        Http::assertSent(fn (Request $request): bool => $request['model'] === 'gpt-4o-mini-tts'
            && $request['voice'] === 'cedar'
            && $request['input'] === 'muñeca'
            && $request['response_format'] === 'wav'
            && $request['speed'] === 1.0);
    }
}
