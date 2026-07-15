<?php

namespace App\Activities\Agent;

use App\Activities\ActivityDefinition;
use App\Models\Student;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

final readonly class OpenAIActivityAgent implements ActivityAgent
{
    public function __construct(private ActivityAgentPrompt $prompt) {}

    public function generate(Student $student, array $messages): ActivityDefinition
    {
        $apiKey = (string) config('activity_agent.openai.api_key');

        if ($apiKey === '') {
            throw new ActivityAgentUnavailable('OpenAI API key is not configured.');
        }

        try {
            $response = Http::baseUrl((string) config('activity_agent.openai.base_url'))
                ->withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->connectTimeout((int) config('activity_agent.openai.connect_timeout'))
                ->timeout((int) config('activity_agent.openai.timeout'))
                ->post('/responses', $this->payload($student, $messages));

            if ($response->failed()) {
                throw new ActivityAgentUnavailable('OpenAI did not complete successfully.');
            }

            $content = json_decode($this->outputText($response->json()), true, flags: JSON_THROW_ON_ERROR);

            if (! is_array($content)) {
                throw new ActivityAgentUnavailable('OpenAI returned an invalid activity.');
            }

            return GeneratedActivityDefinition::fromContent($student, $content);
        } catch (ActivityAgentUnavailable $exception) {
            throw $exception;
        } catch (ConnectionException $exception) {
            throw new ActivityAgentUnavailable('OpenAI is unavailable.', previous: $exception);
        } catch (Throwable $exception) {
            throw new ActivityAgentUnavailable('OpenAI returned an invalid response.', previous: $exception);
        }
    }

    /**
     * @param  list<array{role: 'user'|'assistant', content: string}>  $messages
     * @return array<string, mixed>
     */
    private function payload(Student $student, array $messages): array
    {
        return [
            'model' => config('activity_agent.openai.model'),
            'input' => $this->prompt->build($student, $messages),
            'store' => false,
            'reasoning' => [
                'effort' => config('activity_agent.openai.reasoning_effort'),
            ],
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => 'aprendia_activity',
                    'strict' => true,
                    'schema' => ActivityContentSchema::get(),
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $response */
    private function outputText(array $response): string
    {
        if (($response['status'] ?? null) !== 'completed' || ! is_array($response['output'] ?? null)) {
            throw new ActivityAgentUnavailable('OpenAI returned an incomplete response.');
        }

        foreach ($response['output'] as $output) {
            if (! is_array($output) || ($output['type'] ?? null) !== 'message' || ! is_array($output['content'] ?? null)) {
                continue;
            }

            foreach ($output['content'] as $content) {
                if (is_array($content) && ($content['type'] ?? null) === 'output_text' && is_string($content['text'] ?? null)) {
                    return $content['text'];
                }
            }
        }

        throw new ActivityAgentUnavailable('OpenAI returned an empty response.');
    }
}
