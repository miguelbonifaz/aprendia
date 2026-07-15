<?php

namespace App\Activities\Templates;

use App\Activities\ActivityContentType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class MultipleChoiceTemplateValidator
{
    /** @param array<string, mixed> $payload */
    public static function validate(
        array $payload,
        string $contentKey,
        bool $requiresQuestion,
    ): void {
        Validator::make($payload, self::rules($contentKey, $requiresQuestion))->validate();

        $errors = [];
        $media = self::mediaById($payload);

        foreach ($payload['items'] as $index => $item) {
            $itemId = (string) $item['id'];
            $data = $item['data'];
            self::validateContent($data[$contentKey], "items.{$index}.data.{$contentKey}", $media, $errors);

            $optionIds = [];
            foreach ($data['options'] as $optionIndex => $option) {
                $optionId = (string) $option['id'];
                if (in_array($optionId, $optionIds, true)) {
                    $errors["items.{$index}.data.options.{$optionIndex}.id"][] = 'The option ID must be unique within its item.';
                }
                $optionIds[] = $optionId;
                self::validateContent(
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
    private static function rules(string $contentKey, bool $requiresQuestion): array
    {
        $dataKeys = $requiresQuestion
            ? "{$contentKey},question,options,feedback"
            : "{$contentKey},options,feedback";
        $contentPath = "items.*.data.{$contentKey}";
        $rules = [
            'items.*.data' => ['required', "array:{$dataKeys}"],
            $contentPath => ['required', 'array:type,text,media_id'],
            "{$contentPath}.type" => ['required', Rule::enum(ActivityContentType::class)],
            "{$contentPath}.text" => ['sometimes', 'string', 'max:5000'],
            "{$contentPath}.media_id" => ['sometimes', 'string', 'max:100'],
            'items.*.data.options' => ['required', 'array', 'min:2'],
            'items.*.data.options.*' => ['required', 'array:id,content'],
            'items.*.data.options.*.id' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_-]*$/'],
            'items.*.data.options.*.content' => ['required', 'array:type,text,media_id'],
            'items.*.data.options.*.content.type' => ['required', Rule::enum(ActivityContentType::class)],
            'items.*.data.options.*.content.text' => ['sometimes', 'string', 'max:5000'],
            'items.*.data.options.*.content.media_id' => ['sometimes', 'string', 'max:100'],
            'items.*.data.feedback' => ['required', 'array:correct,incorrect'],
            'items.*.data.feedback.correct' => ['required', 'string', 'max:1000'],
            'items.*.data.feedback.incorrect' => ['required', 'string', 'max:1000'],
        ];

        if ($requiresQuestion) {
            $rules['items.*.data.question'] = ['required', 'string', 'max:1000'];
        }

        return $rules;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, array<string, mixed>>
     */
    private static function mediaById(array $payload): array
    {
        $mediaById = [];
        foreach ($payload['media'] as $media) {
            $mediaById[(string) $media['id']] = $media;
        }

        return $mediaById;
    }

    /**
     * @param array<string, mixed> $content
     * @param array<string, array<string, mixed>> $media
     * @param array<string, list<string>> $errors
     */
    private static function validateContent(array $content, string $path, array $media, array &$errors): void
    {
        $type = ActivityContentType::from((string) $content['type']);
        if ($type === ActivityContentType::Text) {
            if (! is_string($content['text'] ?? null) || trim($content['text']) === '') {
                $errors["{$path}.text"][] = 'Text content must provide text.';
            }
            if (array_key_exists('media_id', $content)) {
                $errors["{$path}.media_id"][] = 'Text content cannot reference media.';
            }

            return;
        }

        if (array_key_exists('text', $content)) {
            $errors["{$path}.text"][] = 'Media content cannot provide text.';
        }

        $mediaId = $content['media_id'] ?? null;
        if (! is_string($mediaId) || ! isset($media[$mediaId])) {
            $errors["{$path}.media_id"][] = 'The referenced media does not exist.';

            return;
        }

        $referencedMedia = $media[$mediaId];
        if (($referencedMedia['type'] ?? null) !== $type->value) {
            $errors["{$path}.media_id"][] = 'The referenced media type does not match the content.';
        }

        $accessibilityKey = $type === ActivityContentType::Image ? 'alt_text' : 'transcript';
        if (! is_string($referencedMedia[$accessibilityKey] ?? null) || trim($referencedMedia[$accessibilityKey]) === '') {
            $errors["media.{$mediaId}.{$accessibilityKey}"][] = "The referenced media requires {$accessibilityKey}.";
        }
    }
}
