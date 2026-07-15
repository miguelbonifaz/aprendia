<?php

namespace App\Activities\Templates;

use App\Activities\ActivityDefinition;
use App\Activities\ActivityResult;
use App\Activities\ActivityTemplate;
use Illuminate\Validation\ValidationException;

final class MultipleChoiceEvaluator
{
    /** @return array{item_id: string, selected_option_id: string, is_correct: bool, message: string, hint: ?string} */
    public function evaluateAnswer(
        ActivityDefinition $activity,
        ActivityTemplate $template,
        string $itemId,
        string $selectedOptionId,
    ): array {
        $payload = $this->payload($activity, $template);
        $item = $this->findItem($payload, $itemId);

        if (! in_array($selectedOptionId, $this->optionIds($item), true)) {
            throw ValidationException::withMessages([
                'selected_option_id' => 'The selected option does not exist for this item.',
            ]);
        }

        $correctOptionId = (string) $payload['answer_key'][$itemId][0];
        $isCorrect = $selectedOptionId === $correctOptionId;
        $data = $item['data'];

        return [
            'item_id' => $itemId,
            'selected_option_id' => $selectedOptionId,
            'is_correct' => $isCorrect,
            'message' => (string) $data['feedback'][$isCorrect ? 'correct' : 'incorrect'],
            'hint' => $isCorrect ? null : (string) $payload['hints'][$itemId][0],
        ];
    }

    /** @param array<string, string> $answers */
    public function evaluateResult(
        ActivityDefinition $activity,
        ActivityTemplate $template,
        array $answers,
    ): ActivityResult {
        $payload = $this->payload($activity, $template);
        $itemIds = array_map(
            static fn (array $item): string => (string) $item['id'],
            $payload['items'],
        );
        $missingItemIds = array_diff($itemIds, array_keys($answers));
        $unknownItemIds = array_diff(array_keys($answers), $itemIds);

        if ($missingItemIds !== []) {
            throw ValidationException::withMessages([
                'answers' => 'Missing answers for: '.implode(', ', $missingItemIds).'.',
            ]);
        }
        if ($unknownItemIds !== []) {
            throw ValidationException::withMessages([
                'answers' => 'Unknown answers for: '.implode(', ', $unknownItemIds).'.',
            ]);
        }

        $correctAnswers = 0;
        foreach ($answers as $itemId => $selectedOptionId) {
            if ($this->evaluateAnswer($activity, $template, $itemId, $selectedOptionId)['is_correct']) {
                $correctAnswers++;
            }
        }

        $totalItems = count($itemIds);
        $score = (int) round(($correctAnswers / $totalItems) * 100);
        $recommendation = $score === 100
            ? 'Continuar con el siguiente objetivo de aprendizaje.'
            : 'Repasar las preguntas incorrectas y volver a intentarlo.';

        return new ActivityResult(
            score: $score,
            summary: "{$correctAnswers} de {$totalItems} respuestas correctas.",
            recommendations: [$recommendation],
        );
    }

    /** @return array<string, mixed> */
    private function payload(ActivityDefinition $activity, ActivityTemplate $template): array
    {
        $payload = $activity->toArray();
        if (($payload['template'] ?? null) !== $template->value) {
            throw ValidationException::withMessages([
                'template' => "The activity must use the {$template->value} template.",
            ]);
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function findItem(array $payload, string $itemId): array
    {
        foreach ($payload['items'] as $item) {
            if (($item['id'] ?? null) === $itemId) {
                return $item;
            }
        }

        throw ValidationException::withMessages([
            'item_id' => 'The activity item does not exist.',
        ]);
    }

    /**
     * @param array<string, mixed> $item
     * @return list<string>
     */
    private function optionIds(array $item): array
    {
        return array_values(array_map(
            static fn (array $option): string => (string) $option['id'],
            $item['data']['options'],
        ));
    }
}
