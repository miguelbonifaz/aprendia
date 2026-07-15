<?php

namespace App\Activities\Templates;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class MatchWithLinesTemplateValidator
{
    /** @param array<string, mixed> $payload */
    public static function validate(array $payload): void
    {
        Validator::make($payload, self::rules())->validate();

        $errors = [];
        $media = ActivityContentValidator::mediaById($payload);
        $leftItemIds = array_column($payload['items'], 'id');
        $rightItemIds = array_map(
            static fn (array $item): string => (string) $item['data']['right']['id'],
            $payload['items'],
        );
        $usedRightItemIds = [];

        foreach ($payload['items'] as $index => $item) {
            $leftItemId = (string) $item['id'];
            $data = $item['data'];
            $rightItemId = (string) $data['right']['id'];

            ActivityContentValidator::validate($data['left'], "items.{$index}.data.left", $media, $errors);
            ActivityContentValidator::validate(
                $data['right']['content'],
                "items.{$index}.data.right.content",
                $media,
                $errors,
            );

            if (in_array($rightItemId, $leftItemIds, true)) {
                $errors["items.{$index}.data.right.id"][] = 'Left and right element IDs must be different.';
            }

            $answers = $payload['answer_key'][$leftItemId] ?? [];
            if (count($answers) !== 1) {
                $errors["answer_key.{$leftItemId}"][] = 'Each left element must have exactly one correct connection.';
            } elseif (! in_array($answers[0], $rightItemIds, true)) {
                $errors["answer_key.{$leftItemId}.0"][] = 'The correct connection must reference an existing right element.';
            } elseif (in_array($answers[0], $usedRightItemIds, true)) {
                $errors["answer_key.{$leftItemId}.0"][] = 'Each right element may only be used once.';
            } else {
                $usedRightItemIds[] = $answers[0];
            }

            if (empty($payload['hints'][$leftItemId] ?? [])) {
                $errors["hints.{$leftItemId}"][] = 'Each connection must provide help for another attempt.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /** @return array<string, array<int, mixed>> */
    private static function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:2'],
            'items.*.data' => ['required', 'array:left,right,feedback'],
            ...ActivityContentValidator::rules('items.*.data.left'),
            'items.*.data.right' => ['required', 'array:id,content'],
            'items.*.data.right.id' => [
                'required',
                'string',
                'max:100',
                'distinct:strict',
                'regex:/^[a-z][a-z0-9_-]*$/',
            ],
            ...ActivityContentValidator::rules('items.*.data.right.content'),
            'items.*.data.feedback' => ['required', 'array:correct,incorrect'],
            'items.*.data.feedback.correct' => ['required', 'string', 'max:1000'],
            'items.*.data.feedback.incorrect' => ['required', 'string', 'max:1000'],
        ];
    }
}
