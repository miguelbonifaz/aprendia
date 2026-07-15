<?php

namespace App\Activities\Templates;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class RecognizeAndSelectTemplateValidator
{
    /** @param array<string, mixed> $payload */
    public static function validate(array $payload): void
    {
        MultipleChoiceTemplateValidator::validate(
            payload: $payload,
            contentKey: 'prompt',
            requiresQuestion: false,
            additionalDataKeys: ['illustration_media_id', 'spoken_word'],
        );

        Validator::make($payload, [
            'items.*.data.illustration_media_id' => ['sometimes', 'string', 'max:100'],
            'items.*.data.spoken_word' => ['sometimes', 'string', 'max:100'],
        ])->validate();

        $media = ActivityContentValidator::mediaById($payload);
        $errors = [];

        foreach ($payload['items'] as $index => $item) {
            $mediaId = $item['data']['illustration_media_id'] ?? null;

            if ($mediaId === null) {
                continue;
            }

            ActivityContentValidator::validate(
                ['type' => 'image', 'media_id' => $mediaId],
                "items.{$index}.data.illustration_media_id",
                $media,
                $errors,
            );
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
