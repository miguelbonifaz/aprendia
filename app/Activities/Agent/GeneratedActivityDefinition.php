<?php

namespace App\Activities\Agent;

use App\Activities\ActivityDefinition;
use App\Models\Student;

final class GeneratedActivityDefinition
{
    /** @param array<string, mixed> $content */
    public static function fromContent(Student $student, array $content): ActivityDefinition
    {
        $items = [];
        $answerKey = [];
        $hints = [];

        foreach ($content['items'] as $item) {
            $itemId = (string) $item['id'];
            $items[] = [
                'id' => $itemId,
                'data' => [
                    'prompt' => ['type' => 'text', 'text' => $item['prompt']],
                    'options' => array_map(
                        static fn (array $option): array => [
                            'id' => $option['id'],
                            'content' => ['type' => 'text', 'text' => $option['text']],
                        ],
                        $item['options'],
                    ),
                    'feedback' => $item['feedback'],
                ],
            ];
            $answerKey[$itemId] = [$item['correct_option_id']];
            $hints[$itemId] = [$item['hint']];
        }

        return ActivityDefinition::fromArray([
            'schema_version' => 1,
            'template' => 'recognize_and_select',
            'title' => $content['title'],
            'instructions' => $content['instructions'],
            'learning_objective' => $content['learning_objective'],
            'difficulty' => $content['difficulty'],
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'birth_date' => $student->birth_date->toDateString(),
                'age' => $student->age,
            ],
            'items' => $items,
            'media' => [],
            'answer_key' => $answerKey,
            'hints' => $hints,
            'result' => null,
        ]);
    }
}
