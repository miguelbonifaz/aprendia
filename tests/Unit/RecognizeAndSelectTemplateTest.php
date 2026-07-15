<?php

namespace Tests\Unit;

use App\Activities\ActivityDefinition;
use App\Activities\Templates\RecognizeAndSelectEvaluator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class RecognizeAndSelectTemplateTest extends TestCase
{
    public function test_text_image_and_audio_content_is_accepted(): void
    {
        $definition = ActivityDefinition::fromArray($this->validPayload());

        $this->assertSame('recognize_and_select', $definition->toArray()['template']);
    }

    public function test_feedback_supports_correction_and_retry_help(): void
    {
        $definition = ActivityDefinition::fromArray($this->validPayload());
        $evaluator = new RecognizeAndSelectEvaluator;

        $correct = $evaluator->evaluateAnswer($definition, 'question_1', 'option_ma');
        $incorrect = $evaluator->evaluateAnswer($definition, 'question_1', 'option_map');

        $this->assertTrue($correct->isCorrect);
        $this->assertSame('¡Muy bien!', $correct->message);
        $this->assertNull($correct->hint);
        $this->assertFalse($incorrect->isCorrect);
        $this->assertSame('Escucha nuevamente.', $incorrect->message);
        $this->assertSame('Fíjate en el sonido inicial.', $incorrect->hint);
    }

    public function test_final_answers_are_scored_without_attempt_penalties(): void
    {
        $payload = $this->validPayload();
        $payload['items'][] = $this->textItem('question_2', 'option_me', 'me');
        $payload['answer_key']['question_2'] = ['option_me'];
        $payload['hints']['question_2'] = ['Busca la vocal e.'];
        $definition = ActivityDefinition::fromArray($payload);

        $result = (new RecognizeAndSelectEvaluator)->evaluateResult($definition, [
            'question_1' => 'option_ma',
            'question_2' => 'option_other',
        ]);

        $this->assertSame(50, $result->score);
        $this->assertSame('1 de 2 respuestas correctas.', $result->summary);
        $this->assertSame(['Repasar las preguntas incorrectas y volver a intentarlo.'], $result->recommendations);
    }

    public function test_options_answers_and_hints_are_strictly_validated(): void
    {
        $duplicateOption = $this->validPayload();
        $duplicateOption['items'][0]['data']['options'][1]['id'] = 'option_ma';
        $this->assertInvalid($duplicateOption, 'items.0.data.options.1.id');

        $multipleAnswers = $this->validPayload();
        $multipleAnswers['answer_key']['question_1'][] = 'option_map';
        $this->assertInvalid($multipleAnswers, 'answer_key.question_1');

        $unknownAnswer = $this->validPayload();
        $unknownAnswer['answer_key']['question_1'] = ['option_unknown'];
        $this->assertInvalid($unknownAnswer, 'answer_key.question_1.0');

        $missingHint = $this->validPayload();
        $missingHint['hints'] = [];
        $this->assertInvalid($missingHint, 'hints.question_1');
    }

    public function test_media_references_type_and_accessibility_are_validated(): void
    {
        $missingMedia = $this->validPayload();
        $missingMedia['items'][0]['data']['prompt']['media_id'] = 'missing';
        $this->assertInvalid($missingMedia, 'items.0.data.prompt.media_id');

        $wrongType = $this->validPayload();
        $wrongType['items'][0]['data']['prompt']['media_id'] = 'image_map';
        $this->assertInvalid($wrongType, 'items.0.data.prompt.media_id');

        $missingTranscript = $this->validPayload();
        $missingTranscript['media'][0]['transcript'] = null;
        $this->assertInvalid($missingTranscript, 'media.audio_ma.transcript');

        $missingAltText = $this->validPayload();
        $missingAltText['media'][1]['alt_text'] = null;
        $this->assertInvalid($missingAltText, 'media.image_map.alt_text');
    }

    public function test_unknown_template_and_evaluation_inputs_are_rejected(): void
    {
        $unknownTemplate = $this->validPayload();
        $unknownTemplate['template'] = 'unknown_template';
        $this->assertInvalid($unknownTemplate, 'template');

        $definition = ActivityDefinition::fromArray($this->validPayload());
        $evaluator = new RecognizeAndSelectEvaluator;

        $this->assertEvaluationInvalid(
            fn () => $evaluator->evaluateAnswer($definition, 'missing', 'option_ma'),
            'item_id',
        );
        $this->assertEvaluationInvalid(
            fn () => $evaluator->evaluateAnswer($definition, 'question_1', 'missing'),
            'selected_option_id',
        );
        $this->assertEvaluationInvalid(fn () => $evaluator->evaluateResult($definition, []), 'answers');
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'schema_version' => 1,
            'template' => 'recognize_and_select',
            'title' => 'Reconoce ma',
            'instructions' => 'Escucha y selecciona la respuesta.',
            'learning_objective' => 'Reconocer la sílaba ma.',
            'difficulty' => 'easy',
            'student' => ['id' => 1, 'name' => 'Alumno Demo', 'birth_date' => '2018-07-14', 'age' => 8],
            'items' => [[
                'id' => 'question_1',
                'data' => [
                    'prompt' => ['type' => 'audio', 'media_id' => 'audio_ma'],
                    'options' => [
                        ['id' => 'option_ma', 'content' => ['type' => 'text', 'text' => 'ma']],
                        ['id' => 'option_map', 'content' => ['type' => 'image', 'media_id' => 'image_map']],
                    ],
                    'feedback' => ['correct' => '¡Muy bien!', 'incorrect' => 'Escucha nuevamente.'],
                ],
            ]],
            'media' => [
                ['id' => 'audio_ma', 'type' => 'audio', 'source' => 'audio/ma.mp3', 'transcript' => 'ma'],
                ['id' => 'image_map', 'type' => 'image', 'source' => 'images/mapa.png', 'alt_text' => 'Un mapa'],
            ],
            'answer_key' => ['question_1' => ['option_ma']],
            'hints' => ['question_1' => ['Fíjate en el sonido inicial.']],
            'result' => null,
        ];
    }

    /** @return array<string, mixed> */
    private function textItem(string $itemId, string $correctOptionId, string $text): array
    {
        return [
            'id' => $itemId,
            'data' => [
                'prompt' => ['type' => 'text', 'text' => "Selecciona {$text}."],
                'options' => [
                    ['id' => $correctOptionId, 'content' => ['type' => 'text', 'text' => $text]],
                    ['id' => 'option_other', 'content' => ['type' => 'text', 'text' => 'otra']],
                ],
                'feedback' => ['correct' => '¡Correcto!', 'incorrect' => 'Intenta otra vez.'],
            ],
        ];
    }

    /** @param array<string, mixed> $payload */
    private function assertInvalid(array $payload, string $errorKey): void
    {
        $this->assertEvaluationInvalid(fn () => ActivityDefinition::fromArray($payload), $errorKey);
    }

    private function assertEvaluationInvalid(callable $callback, string $errorKey): void
    {
        try {
            $callback();
            $this->fail('Validation should fail.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey($errorKey, $exception->errors());
        }
    }
}
