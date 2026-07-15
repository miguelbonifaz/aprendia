<?php

namespace App\Activities\Templates;

use App\Activities\ActivityDefinition;
use App\Activities\ActivityResult;
use App\Activities\ActivityTemplate;

final class RecognizeAndSelectEvaluator
{
    public function evaluateAnswer(
        ActivityDefinition $activity,
        string $itemId,
        string $selectedOptionId,
    ): RecognizeAndSelectFeedback {
        $feedback = (new MultipleChoiceEvaluator)->evaluateAnswer(
            $activity,
            ActivityTemplate::RecognizeAndSelect,
            $itemId,
            $selectedOptionId,
        );

        return new RecognizeAndSelectFeedback(
            itemId: $feedback['item_id'],
            selectedOptionId: $feedback['selected_option_id'],
            isCorrect: $feedback['is_correct'],
            message: $feedback['message'],
            hint: $feedback['hint'],
        );
    }

    /** @param array<string, string> $answers */
    public function evaluateResult(ActivityDefinition $activity, array $answers): ActivityResult
    {
        return (new MultipleChoiceEvaluator)->evaluateResult(
            $activity,
            ActivityTemplate::RecognizeAndSelect,
            $answers,
        );
    }
}
