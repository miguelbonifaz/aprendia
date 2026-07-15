<?php

namespace App\Activities\Agent;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

final readonly class OpenAIActivitySpeech
{
    public function generate(string $spokenWord): string
    {
        $apiKey = (string) config('activity_agent.openai.api_key');

        if ($apiKey === '') {
            throw new ActivityAgentUnavailable('OpenAI API key is not configured.');
        }

        try {
            $response = Http::baseUrl((string) config('activity_agent.openai.base_url'))
                ->withToken($apiKey)
                ->accept((string) config('activity_agent.openai.speech_mime_type'))
                ->asJson()
                ->connectTimeout((int) config('activity_agent.openai.connect_timeout'))
                ->timeout((int) config('activity_agent.openai.speech_timeout'))
                ->post('/audio/speech', [
                    'model' => config('activity_agent.openai.speech_model'),
                    'voice' => config('activity_agent.openai.speech_voice'),
                    'input' => $spokenWord,
                    'response_format' => config('activity_agent.openai.speech_format'),
                    'speed' => config('activity_agent.openai.speech_speed'),
                ]);

            if ($response->failed() || $response->body() === '' || ! Str::startsWith((string) $response->header('Content-Type'), 'audio/')) {
                throw new ActivityAgentUnavailable('OpenAI did not generate activity speech.');
            }

            return $response->body();
        } catch (ActivityAgentUnavailable $exception) {
            throw $exception;
        } catch (ConnectionException $exception) {
            throw new ActivityAgentUnavailable('OpenAI speech is unavailable.', previous: $exception);
        } catch (Throwable $exception) {
            throw new ActivityAgentUnavailable('OpenAI returned invalid speech.', previous: $exception);
        }
    }
}
