<?php

namespace App\Activities\Templates;

final class ListenReadAndRespondTemplateValidator
{
    /** @param array<string, mixed> $payload */
    public static function validate(array $payload): void
    {
        MultipleChoiceTemplateValidator::validate(
            payload: $payload,
            contentKey: 'stimulus',
            requiresQuestion: true,
        );
    }
}
