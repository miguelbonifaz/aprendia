<?php

namespace App\Activities\Templates;

final class RecognizeAndSelectTemplateValidator
{
    /** @param array<string, mixed> $payload */
    public static function validate(array $payload): void
    {
        MultipleChoiceTemplateValidator::validate(
            payload: $payload,
            contentKey: 'prompt',
            requiresQuestion: false,
        );
    }
}
