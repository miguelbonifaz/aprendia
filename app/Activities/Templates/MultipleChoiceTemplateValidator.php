<?php

namespace App\Activities\Templates;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class MultipleChoiceTemplateValidator
{
    /** @param array<string, mixed> $payload */
    public static function validate(
        array $payload,
        string $contentKey,
        bool $requiresQuestion,
        array $additionalDataKeys = [],
    ): void {
        Validator::make($payload, self::rules($contentKey, $requiresQuestion, $additionalDataKeys))->validate();

        $errors = [];
        $media = ActivityContentValidator::mediaById($payload);

        foreach ($payload['items'] as $index => $item) {
            $itemId = (string) $item['id'];
            $data = $item['data'];
            ActivityContentValidator::validate(
                $data[$contentKey],
                "items.{$index}.data.{$contentKey}",
                $media,
                $errors,
            );

            $optionIds = [];
            foreach ($data['options'] as $optionIndex => $option) {
                $optionId = (string) $option['id'];
                if (in_array($optionId, $optionIds, true)) {
                    $errors["items.{$index}.data.options.{$optionIndex}.id"][] = 'The option ID must be unique within its item.';
                }
                $optionIds[] = $optionId;
                ActivityContentValidator::validate(
                    $option['content'],
                    "items.{$index}.data.options.{$optionIndex}.content",
                    $media,
                    $errors,
                );
            }

            $answers = $payload['answer_key'][$itemId] ?? [];
            if (count($answers) !== 1) {
                $errors["answer_key.{$itemId}"][] = 'The item must have exactly one correct answer.';
            } elseif (! in_array($answers[0], $optionIds, true)) {
                $errors["answer_key.{$itemId}.0"][] = 'The correct answer must reference an existing option.';
            }

            if (empty($payload['hints'][$itemId] ?? [])) {
                $errors["hints.{$itemId}"][] = 'The item must provide help for another attempt.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /** @return array<string, array<int, mixed>> */
    private static function rules(string $contentKey, bool $requiresQuestion, array $additionalDataKeys): array
    {
        $dataKeys = [$contentKey, 'options', 'feedback', ...$additionalDataKeys];

        if ($requiresQuestion) {
            $dataKeys[] = 'question';
        }

        $contentPath = "items.*.data.{$contentKey}";
        $rules = [
            'items.*.data' => ['required', 'array:'.implode(',', $dataKeys)],
            ...ActivityContentValidator::rules($contentPath),
            'items.*.data.options' => ['required', 'array', 'min:2'],
            'items.*.data.options.*' => ['required', 'array:id,content'],
            'items.*.data.options.*.id' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_-]*$/'],
            ...ActivityContentValidator::rules('items.*.data.options.*.content'),
            'items.*.data.feedback' => ['required', 'array:correct,incorrect'],
            'items.*.data.feedback.correct' => ['required', 'string', 'max:1000'],
            'items.*.data.feedback.incorrect' => ['required', 'string', 'max:1000'],
        ];

        if ($requiresQuestion) {
            $rules['items.*.data.question'] = ['required', 'string', 'max:1000'];
        }

        return $rules;
    }
}
