<?php

namespace Tests\Unit;

use App\Activities\ActivityDefinition;
use App\Activities\Templates\ListenReadAndRespondEvaluator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class ListenReadAndRespondTemplateTest extends TestCase
{
    public function test_text_image_and_audio_stimuli_are_accepted_without_replay_limits(): void
    {
        $audio = ActivityDefinition::fromArray($this->validPayload())->toArray();

        $textPayload = $this->validPayload();
        $textPayload['items'][0]['data']['stimulus'] = [
            'type' => 'text',
            'text' => 'Marta tiene un gato.',
        ];
        $text = ActivityDefinition::fromArray($textPayload)->toArray();

        $imagePayload = $this->validPayload();
        $imagePayload['items'][0]['data']['stimulus'] = [
            'type' => 'image',
            'media_id' => 'image_cat',
        ];
        $image = ActivityDefinition::fromArray($imagePayload)->toArray();

        $this->assertSame('audio', $audio['items'][0]['data']['stimulus']['type']);
        $this->assertSame('text', $text['items'][0]['data']['stimulus']['type']);
        $this->assertSame('image', $image['items'][0]['data']['stimulus']['type']);
        $this->assertArrayNotHasKey('replay_limit', $audio['items'][0]['data']);
    }

    public function test_questions_can_reuse_the_same_audio_reference(): void
    {
        $payload = $this->payloadWithTwoQuestions();
        $definition = ActivityDefinition::fromArray($payload)->toArray();

        $this->assertSame(
            $definition['items'][0]['data']['stimulus']['media_id'],
            $definition['items'][1]['data']['stimulus']['media_id'],
        );
    }

    public function test_feedback_and_final_result_use_final_answers(): void
    {
        $definition = ActivityDefinition::fromArray($this->payloadWithTwoQuestions());
        $evaluator = new ListenReadAndRespondEvaluator;

        $incorrect = $evaluator->evaluateAnswer($definition, 'question_1', 'option_dog');
        $correct = $evaluator->evaluateAnswer($definition, 'question_1', 'option_cat');
        $result = $evaluator->evaluateResult($definition, [
            'question_1' => 'option_cat',
            'question_2' => 'option_dog',
        ]);

        $this->assertFalse($incorrect->isCorrect);
        $this->assertSame('Escucha una vez más.', $incorrect->hint);
        $this->assertTrue($correct->isCorrect);
        $this->assertNull($correct->hint);
        $this->assertSame(50, $result->score);
        $this->assertSame('1 de 2 respuestas correctas.', $result->summary);
    }

    public function test_question_options_answer_and_help_are_required(): void
    {
        $missingQuestion = $this->validPayload();
        unset($missingQuestion['items'][0]['data']['question']);
        $this->assertInvalid($missingQuestion, 'items.0.data.question');

        $duplicateOption = $this->validPayload();
        $duplicateOption['items'][0]['data']['options'][1]['id'] = 'option_cat';
        $this->assertInvalid($duplicateOption, 'items.0.data.options.1.id');

        $multipleAnswers = $this->validPayload();
        $multipleAnswers['answer_key']['question_1'][] = 'option_dog';
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
        $missingMedia['items'][0]['data']['stimulus']['media_id'] = 'missing';
        $this->assertInvalid($missingMedia, 'items.0.data.stimulus.media_id');

        $wrongType = $this->validPayload();
        $wrongType['items'][0]['data']['stimulus']['media_id'] = 'image_cat';
        $this->assertInvalid($wrongType, 'items.0.data.stimulus.media_id');

        $missingTranscript = $this->validPayload();
        $missingTranscript['media'][0]['transcript'] = null;
        $this->assertInvalid($missingTranscript, 'media.audio_story.transcript');

        $missingAltText = $this->validPayload();
        $missingAltText['media'][1]['alt_text'] = null;
        $this->assertInvalid($missingAltText, 'media.image_cat.alt_text');
    }

    public function test_incomplete_or_unknown_evaluation_inputs_are_rejected(): void
    {
        $definition = ActivityDefinition::fromArray($this->validPayload());
        $evaluator = new ListenReadAndRespondEvaluator;

        $this->assertEvaluationInvalid(
            fn () => $evaluator->evaluateAnswer($definition, 'missing', 'option_cat'),
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
            'template' => 'listen_read_and_respond',
            'title' => 'Escucha la historia',
            'instructions' => 'Escucha y responde.',
            'learning_objective' => 'Comprender una frase breve.',
            'difficulty' => 'easy',
            'student' => ['id' => 1, 'name' => 'Alumno Demo', 'birth_date' => '2018-07-14', 'age' => 8],
            'items' => [[
                'id' => 'question_1',
                'data' => [
                    'stimulus' => ['type' => 'audio', 'media_id' => 'audio_story'],
                    'question' => '¿Qué animal tiene Marta?',
                    'options' => [
                        ['id' => 'option_cat', 'content' => ['type' => 'image', 'media_id' => 'image_cat']],
                        ['id' => 'option_dog', 'content' => ['type' => 'image', 'media_id' => 'image_dog']],
                    ],
                    'feedback' => ['correct' => '¡Comprendiste!', 'incorrect' => 'Intenta nuevamente.'],
                ],
            ]],
            'media' => [
                ['id' => 'audio_story', 'type' => 'audio', 'source' => 'audio/story.mp3', 'transcript' => 'Marta tiene un gato.'],
                ['id' => 'image_cat', 'type' => 'image', 'source' => 'images/cat.png', 'alt_text' => 'Un gato'],
                ['id' => 'image_dog', 'type' => 'image', 'source' => 'images/dog.png', 'alt_text' => 'Un perro'],
            ],
            'answer_key' => ['question_1' => ['option_cat']],
            'hints' => ['question_1' => ['Escucha una vez más.']],
            'result' => null,
        ];
    }

    /** @return array<string, mixed> */
    private function payloadWithTwoQuestions(): array
    {
        $payload = $this->validPayload();
        $payload['items'][] = [
            'id' => 'question_2',
            'data' => [
                'stimulus' => ['type' => 'audio', 'media_id' => 'audio_story'],
                'question' => '¿Quién tiene un gato?',
                'options' => [
                    ['id' => 'option_marta', 'content' => ['type' => 'text', 'text' => 'Marta']],
                    ['id' => 'option_dog', 'content' => ['type' => 'text', 'text' => 'El perro']],
                ],
                'feedback' => ['correct' => '¡Correcto!', 'incorrect' => 'Vuelve a escuchar.'],
            ],
        ];
        $payload['answer_key']['question_2'] = ['option_marta'];
        $payload['hints']['question_2'] = ['Escucha el nombre al inicio.'];

        return $payload;
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
