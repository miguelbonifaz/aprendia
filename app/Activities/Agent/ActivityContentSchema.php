<?php

namespace App\Activities\Agent;

final class ActivityContentSchema
{
    /** @return array<string, mixed> */
    public static function get(): array
    {
        $text = ['type' => 'string', 'minLength' => 1];
        $identifier = [
            'type' => 'string',
            'pattern' => '^[a-z][a-z0-9_-]*$',
        ];

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['title', 'instructions', 'learning_objective', 'difficulty', 'items'],
            'properties' => [
                'title' => $text,
                'instructions' => $text,
                'learning_objective' => $text,
                'difficulty' => ['type' => 'string', 'enum' => ['easy', 'medium', 'hard']],
                'items' => [
                    'type' => 'array',
                    'minItems' => 3,
                    'maxItems' => 3,
                    'items' => self::item($text, $identifier),
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $text
     * @param  array<string, mixed>  $identifier
     * @return array<string, mixed>
     */
    private static function item(array $text, array $identifier): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['id', 'prompt', 'image_alt_text', 'spoken_word', 'options', 'correct_option_id', 'hint', 'feedback'],
            'properties' => [
                'id' => $identifier,
                'prompt' => $text,
                'image_alt_text' => $text,
                'spoken_word' => $text,
                'options' => [
                    'type' => 'array',
                    'minItems' => 3,
                    'maxItems' => 3,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['id', 'text'],
                        'properties' => ['id' => $identifier, 'text' => $text],
                    ],
                ],
                'correct_option_id' => $identifier,
                'hint' => $text,
                'feedback' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['correct', 'incorrect'],
                    'properties' => ['correct' => $text, 'incorrect' => $text],
                ],
            ],
        ];
    }
}
