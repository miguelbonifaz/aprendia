<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'public_id' => (string) Str::uuid(),
            'definition' => [
                'schema_version' => 1,
                'template' => 'recognize_and_select',
                'title' => 'Reconocer una palabra',
                'instructions' => 'Lee y elige la respuesta correcta.',
                'learning_objective' => 'Reconocer una palabra entre varias opciones.',
                'difficulty' => 'easy',
                'student' => [
                    'id' => 1,
                    'name' => 'Alumno de prueba',
                    'birth_date' => '2018-01-01',
                    'age' => 8,
                ],
                'items' => [[
                    'id' => 'question_1',
                    'data' => [
                        'prompt' => ['type' => 'text', 'text' => 'Selecciona casa.'],
                        'options' => [
                            ['id' => 'option_casa', 'content' => ['type' => 'text', 'text' => 'casa']],
                            ['id' => 'option_mesa', 'content' => ['type' => 'text', 'text' => 'mesa']],
                        ],
                        'feedback' => ['correct' => '¡Muy bien!', 'incorrect' => 'Intenta nuevamente.'],
                    ],
                ]],
                'media' => [],
                'answer_key' => ['question_1' => ['option_casa']],
                'hints' => ['question_1' => ['Busca la palabra que comienza con c.']],
                'result' => null,
            ],
        ];
    }
}
