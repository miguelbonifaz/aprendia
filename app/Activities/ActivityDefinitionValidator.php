<?php

namespace App\Activities;

use App\Activities\Templates\ListenReadAndRespondTemplateValidator;
use App\Activities\Templates\MatchWithLinesTemplateValidator;
use App\Activities\Templates\RecognizeAndSelectTemplateValidator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator as LaravelValidator;

final class ActivityDefinitionValidator
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function validate(array $payload): array
    {
        $validator = Validator::make($payload, self::rules($payload));
        $validator->after(fn (LaravelValidator $validator) => self::validateReferences($validator, $payload));
        $validated = $validator->validate();

        $definition = [
            'schema_version' => $validated['schema_version'],
            'template' => $validated['template'],
            'title' => $validated['title'],
            'instructions' => $validated['instructions'],
            'learning_objective' => $validated['learning_objective'],
            'difficulty' => $validated['difficulty'],
            'student' => $validated['student'],
            'items' => $validated['items'],
            'media' => $validated['media'] ?? [],
            'answer_key' => $validated['answer_key'],
            'hints' => $validated['hints'] ?? [],
            'result' => $validated['result'] ?? null,
        ];

        match (ActivityTemplate::from((string) $definition['template'])) {
            ActivityTemplate::RecognizeAndSelect => RecognizeAndSelectTemplateValidator::validate($definition),
            ActivityTemplate::ListenReadAndRespond => ListenReadAndRespondTemplateValidator::validate($definition),
            ActivityTemplate::MatchWithLines => MatchWithLinesTemplateValidator::validate($definition),
        };

        return $definition;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, array<int, mixed>>
     */
    private static function rules(array $payload): array
    {
        $hasResult = is_array($payload['result'] ?? null);

        return [
            'schema_version' => ['required', 'integer', Rule::in([1])],
            'template' => ['required', Rule::enum(ActivityTemplate::class)],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string', 'max:5000'],
            'learning_objective' => ['required', 'string', 'max:1000'],
            'difficulty' => ['required', Rule::enum(ActivityDifficulty::class)],
            'student' => ['required', 'array:id,name,birth_date,age'],
            'student.id' => ['required', 'integer', 'min:1'],
            'student.name' => ['required', 'string', 'max:255'],
            'student.birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'student.age' => ['required', 'integer', 'between:0,120'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'array:id,data'],
            'items.*.id' => ['required', 'string', 'max:100', 'distinct:strict', 'regex:/^[a-z][a-z0-9_-]*$/'],
            'items.*.data' => ['present', 'array'],
            'media' => ['sometimes', 'array'],
            'media.*' => ['required', 'array:id,type,source,alt_text,transcript'],
            'media.*.id' => ['required', 'string', 'max:100', 'distinct:strict', 'regex:/^[a-z][a-z0-9_-]*$/'],
            'media.*.type' => ['required', Rule::enum(ActivityMediaType::class)],
            'media.*.source' => ['required', 'string', 'max:2048'],
            'media.*.alt_text' => ['nullable', 'string', 'max:500'],
            'media.*.transcript' => ['nullable', 'string', 'max:5000'],
            'answer_key' => ['required', 'array', 'min:1'],
            'answer_key.*' => ['required', 'array', 'min:1'],
            'answer_key.*.*' => ['required', 'string', 'max:255'],
            'hints' => ['sometimes', 'array'],
            'hints.*' => ['required', 'array', 'min:1'],
            'hints.*.*' => ['required', 'string', 'max:1000'],
            'result' => ['nullable', 'array:score,summary,recommendations'],
            'result.score' => [Rule::requiredIf($hasResult), 'integer', 'between:0,100'],
            'result.summary' => [Rule::requiredIf($hasResult), 'string', 'max:2000'],
            'result.recommendations' => [Rule::requiredIf($hasResult), 'array', 'min:1'],
            'result.recommendations.*' => ['required', 'string', 'max:1000'],
        ];
    }

    /** @param array<string, mixed> $payload */
    private static function validateReferences(LaravelValidator $validator, array $payload): void
    {
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
        $itemIds = collect($items)
            ->filter(fn (mixed $item): bool => is_array($item) && is_string($item['id'] ?? null))
            ->pluck('id')
            ->all();
        $answerKey = is_array($payload['answer_key'] ?? null) ? $payload['answer_key'] : [];
        $hints = is_array($payload['hints'] ?? null) ? $payload['hints'] : [];

        foreach (array_diff($itemIds, array_keys($answerKey)) as $itemId) {
            $validator->errors()->add('answer_key', "Missing answers for item {$itemId}.");
        }

        foreach (array_diff(array_keys($answerKey), $itemIds) as $itemId) {
            $validator->errors()->add("answer_key.{$itemId}", 'The referenced item does not exist.');
        }

        foreach (array_diff(array_keys($hints), $itemIds) as $itemId) {
            $validator->errors()->add("hints.{$itemId}", 'The referenced item does not exist.');
        }
    }
}
