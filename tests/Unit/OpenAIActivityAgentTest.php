<?php

namespace Tests\Unit;

use App\Activities\ActivityDefinition;
use App\Activities\Agent\ActivityAgent;
use App\Activities\Agent\ActivityAgentUnavailable;
use App\Activities\Agent\OpenAIActivityAgent;
use App\Models\Student;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class OpenAIActivityAgentTest extends TestCase
{
    public function test_it_generates_an_activity_with_the_responses_api(): void
    {
        config([
            'activity_agent.driver' => 'openai',
            'activity_agent.openai.api_key' => 'test-key',
            'activity_agent.openai.model' => 'gpt-5.6-luna',
            'activity_agent.openai.reasoning_effort' => 'max',
        ]);
        Http::preventStrayRequests();
        Http::fake(['api.openai.com/v1/responses' => Http::response($this->responsePayload())]);

        $agent = app(ActivityAgent::class);
        $activity = $agent->generate(
            $this->student(),
            [['role' => 'user', 'content' => 'Practicar la letra m.']],
        );

        $this->assertInstanceOf(OpenAIActivityAgent::class, $agent);
        $this->assertInstanceOf(ActivityDefinition::class, $activity);
        $this->assertSame('Reconoce la letra m', $activity->toArray()['title']);
        $this->assertCount(3, $activity->toArray()['media']);
        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://api.openai.com/v1/responses'
                && $request['model'] === 'gpt-5.6-luna'
                && $request['reasoning']['effort'] === 'max'
                && $request['text']['format']['type'] === 'json_schema'
                && $request['text']['format']['strict'] === true
                && $request['store'] === false;
        });
    }

    public function test_it_hides_failed_api_response_details(): void
    {
        config(['activity_agent.openai.api_key' => 'test-key']);
        Http::preventStrayRequests();
        Http::fake(['api.openai.com/v1/responses' => Http::response([
            'error' => ['message' => 'sensitive provider detail'],
        ], 500)]);

        try {
            app(OpenAIActivityAgent::class)->generate(
                $this->student(),
                [['role' => 'user', 'content' => 'Practicar vocales.']],
            );
            $this->fail('The agent should be unavailable.');
        } catch (ActivityAgentUnavailable $exception) {
            $this->assertSame('OpenAI did not complete successfully.', $exception->getMessage());
            $this->assertStringNotContainsString('sensitive provider detail', $exception->getMessage());
        }
    }

    private function student(): Student
    {
        $student = new Student([
            'name' => 'Ana',
            'birth_date' => now()->subYears(8)->toDateString(),
        ]);
        $student->id = 7;

        return $student;
    }

    /** @return array<string, mixed> */
    private function responsePayload(): array
    {
        $content = [
            'title' => 'Reconoce la letra m',
            'instructions' => 'Selecciona la respuesta correcta.',
            'learning_objective' => 'Reconocer la letra m.',
            'difficulty' => 'easy',
            'items' => [],
        ];

        foreach (range(1, 3) as $number) {
            $content['items'][] = [
                'id' => "question_{$number}",
                'prompt' => '¿Cuál palabra comienza con m?',
                'image_alt_text' => 'Una mesa infantil de madera sobre un fondo claro.',
                'options' => [
                    ['id' => "mesa_{$number}", 'text' => 'mesa'],
                    ['id' => "casa_{$number}", 'text' => 'casa'],
                    ['id' => "pato_{$number}", 'text' => 'pato'],
                ],
                'correct_option_id' => "mesa_{$number}",
                'hint' => 'Pronuncia cada palabra lentamente.',
                'feedback' => ['correct' => '¡Muy bien!', 'incorrect' => 'Intenta nuevamente.'],
            ];
        }

        return [
            'status' => 'completed',
            'output' => [[
                'type' => 'message',
                'content' => [[
                    'type' => 'output_text',
                    'text' => json_encode($content, JSON_THROW_ON_ERROR),
                ]],
            ]],
        ];
    }
}
