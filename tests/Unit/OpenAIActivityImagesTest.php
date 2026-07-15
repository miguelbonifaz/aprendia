<?php

namespace Tests\Unit;

use App\Activities\ActivityDefinition;
use App\Activities\Agent\OpenAIActivityImages;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class OpenAIActivityImagesTest extends TestCase
{
    public function test_it_generates_each_activity_image_with_openai(): void
    {
        config([
            'activity_agent.openai.api_key' => 'test-key',
            'activity_agent.openai.image_model' => 'gpt-image-2',
            'activity_agent.openai.image_size' => '1024x1024',
            'activity_agent.openai.image_quality' => 'low',
            'activity_agent.openai.image_compression' => 70,
        ]);
        Http::preventStrayRequests();
        Http::fake(['api.openai.com/v1/images/generations' => Http::response([
            'data' => [['b64_json' => base64_encode('image-content')]],
        ])]);

        $media = app(OpenAIActivityImages::class)->generate($this->definition());

        $this->assertCount(1, $media);
        $this->assertSame('question_1_image', $media[0]['media_id']);
        $this->assertSame('image/webp', $media[0]['mime_type']);
        $this->assertSame(base64_encode('image-content'), $media[0]['content']);
        Http::assertSent(fn (Request $request): bool => $request['model'] === 'gpt-image-2'
            && $request['output_format'] === 'webp'
            && $request['quality'] === 'low'
            && $request['output_compression'] === 70
            && str_contains($request['prompt'], 'Una mano abierta'));
    }

    private function definition(): ActivityDefinition
    {
        return ActivityDefinition::fromArray([
            'schema_version' => 1,
            'template' => 'recognize_and_select',
            'title' => 'Practica ma',
            'instructions' => 'Mira la imagen y selecciona la sílaba.',
            'learning_objective' => 'Reconocer la sílaba ma.',
            'difficulty' => 'easy',
            'student' => ['id' => 1, 'name' => 'Ana', 'birth_date' => '2018-01-01', 'age' => 8],
            'items' => [[
                'id' => 'question_1',
                'data' => [
                    'prompt' => ['type' => 'text', 'text' => '¿Cómo comienza mano?'],
                    'illustration_media_id' => 'question_1_image',
                    'options' => [
                        ['id' => 'ma', 'content' => ['type' => 'text', 'text' => 'ma']],
                        ['id' => 'me', 'content' => ['type' => 'text', 'text' => 'me']],
                    ],
                    'feedback' => ['correct' => '¡Bien!', 'incorrect' => 'Intenta otra vez.'],
                ],
            ]],
            'media' => [[
                'id' => 'question_1_image',
                'type' => 'image',
                'source' => 'generated://question_1_image',
                'alt_text' => 'Una mano abierta',
                'transcript' => null,
            ]],
            'answer_key' => ['question_1' => ['ma']],
            'hints' => ['question_1' => ['Di la palabra mano lentamente.']],
            'result' => null,
        ]);
    }
}
