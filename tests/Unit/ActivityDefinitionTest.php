<?php

namespace Tests\Unit;

use App\Activities\ActivityDefinition;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class ActivityDefinitionTest extends TestCase
{
    public function test_complete_definition_is_validated_and_serialized(): void
    {
        $payload = $this->validPayload();
        $definition = ActivityDefinition::fromArray($payload);

        $this->assertSame($payload, $definition->toArray());
        $this->assertSame($payload, $definition->jsonSerialize());
        $this->assertSame($payload, json_decode(json_encode($definition), true));
    }

    public function test_optional_fields_receive_stable_defaults(): void
    {
        $payload = $this->validPayload();
        unset($payload['media'], $payload['result']);

        $definition = ActivityDefinition::fromArray($payload)->toArray();

        $this->assertSame([], $definition['media']);
        $this->assertNull($definition['result']);
    }

    public function test_common_fields_and_enums_are_validated(): void
    {
        $missingTitle = $this->validPayload();
        unset($missingTitle['title']);
        $this->assertInvalid($missingTitle, 'title');

        $invalidDifficulty = $this->validPayload();
        $invalidDifficulty['difficulty'] = 'expert';
        $this->assertInvalid($invalidDifficulty, 'difficulty');

        $invalidMedia = $this->validPayload();
        $invalidMedia['media'][0]['type'] = 'video';
        $this->assertInvalid($invalidMedia, 'media.0.type');
    }

    public function test_item_and_media_identifiers_must_be_unique(): void
    {
        $duplicateItems = $this->validPayload();
        $duplicateItems['items'][] = $duplicateItems['items'][0];
        $this->assertInvalid($duplicateItems, 'items.1.id');

        $duplicateMedia = $this->validPayload();
        $duplicateMedia['media'][] = $duplicateMedia['media'][0];
        $this->assertInvalid($duplicateMedia, 'media.1.id');
    }

    public function test_answers_and_hints_must_reference_existing_items(): void
    {
        $unknownAnswer = $this->validPayload();
        $unknownAnswer['answer_key']['question_2'] = ['option_2'];
        $this->assertInvalid($unknownAnswer, 'answer_key.question_2');

        $unknownHint = $this->validPayload();
        $unknownHint['hints']['question_2'] = ['Ayuda inválida.'];
        $this->assertInvalid($unknownHint, 'hints.question_2');
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'schema_version' => 1,
            'template' => 'recognize_and_select',
            'title' => 'Reconoce la sílaba ma',
            'instructions' => 'Selecciona la respuesta correcta.',
            'learning_objective' => 'Reconocer la sílaba ma.',
            'difficulty' => 'easy',
            'student' => ['id' => 1, 'name' => 'Alumno Demo', 'birth_date' => '2018-07-14', 'age' => 8],
            'items' => [[
                'id' => 'question_1',
                'data' => [
                    'prompt' => ['type' => 'text', 'text' => 'Selecciona ma.'],
                    'options' => [
                        ['id' => 'option_ma', 'content' => ['type' => 'text', 'text' => 'ma']],
                        ['id' => 'option_pa', 'content' => ['type' => 'text', 'text' => 'pa']],
                    ],
                    'feedback' => ['correct' => '¡Correcto!', 'incorrect' => 'Intenta otra vez.'],
                ],
            ]],
            'media' => [[
                'id' => 'audio_ma',
                'type' => 'audio',
                'source' => 'activities/audio-ma.mp3',
                'transcript' => 'ma',
            ]],
            'answer_key' => ['question_1' => ['option_ma']],
            'hints' => ['question_1' => ['Escucha el sonido inicial.']],
            'result' => [
                'score' => 100,
                'summary' => 'Reconoció correctamente la sílaba.',
                'recommendations' => ['Continuar con me, mi, mo y mu.'],
            ],
        ];
    }

    /** @param array<string, mixed> $payload */
    private function assertInvalid(array $payload, string $errorKey): void
    {
        try {
            ActivityDefinition::fromArray($payload);
            $this->fail('The activity definition should be invalid.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey($errorKey, $exception->errors());
        }
    }
}
