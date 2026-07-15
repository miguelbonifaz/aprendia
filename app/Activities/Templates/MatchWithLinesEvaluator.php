<?php

namespace App\Activities\Templates;

use App\Activities\ActivityDefinition;
use App\Activities\ActivityResult;
use App\Activities\ActivityTemplate;
use Illuminate\Validation\ValidationException;

final class MatchWithLinesEvaluator
{
    public function evaluateConnection(
        ActivityDefinition $activity,
        string $leftItemId,
        string $rightItemId,
    ): MatchWithLinesFeedback {
        $payload = $this->payload($activity);
        $item = $this->findLeftItem($payload, $leftItemId);

        if (! in_array($rightItemId, $this->rightItemIds($payload), true)) {
            throw ValidationException::withMessages([
                'right_item_id' => 'The right element does not exist.',
            ]);
        }

        $isCorrect = $rightItemId === (string) $payload['answer_key'][$leftItemId][0];
        $feedback = $item['data']['feedback'];

        return new MatchWithLinesFeedback(
            leftItemId: $leftItemId,
            rightItemId: $rightItemId,
            isCorrect: $isCorrect,
            message: (string) $feedback[$isCorrect ? 'correct' : 'incorrect'],
            hint: $isCorrect ? null : (string) $payload['hints'][$leftItemId][0],
        );
    }

    /** @param array<string, string> $answers */
    public function evaluateResult(ActivityDefinition $activity, array $answers): ActivityResult
    {
        $payload = $this->payload($activity);
        $leftItemIds = array_map(
            static fn (array $item): string => (string) $item['id'],
            $payload['items'],
        );
        $missingItemIds = array_diff($leftItemIds, array_keys($answers));
        $unknownItemIds = array_diff(array_keys($answers), $leftItemIds);

        if ($missingItemIds !== []) {
            throw ValidationException::withMessages([
                'answers' => 'Missing connections for: '.implode(', ', $missingItemIds).'.',
            ]);
        }
        if ($unknownItemIds !== []) {
            throw ValidationException::withMessages([
                'answers' => 'Unknown left elements: '.implode(', ', $unknownItemIds).'.',
            ]);
        }
        if (count(array_unique($answers)) !== count($answers)) {
            throw ValidationException::withMessages([
                'answers' => 'Each right element may only be connected once.',
            ]);
        }

        $correctConnections = 0;
        foreach ($answers as $leftItemId => $rightItemId) {
            if ($this->evaluateConnection($activity, $leftItemId, $rightItemId)->isCorrect) {
                $correctConnections++;
            }
        }

        $totalConnections = count($leftItemIds);
        $score = (int) round(($correctConnections / $totalConnections) * 100);
        $recommendation = $score === 100
            ? 'Continuar con el siguiente objetivo de aprendizaje.'
            : 'Corregir las relaciones incorrectas y volver a intentarlo.';

        return new ActivityResult(
            score: $score,
            summary: "{$correctConnections} de {$totalConnections} relaciones correctas.",
            recommendations: [$recommendation],
        );
    }

    /** @return array<string, mixed> */
    private function payload(ActivityDefinition $activity): array
    {
        $payload = $activity->toArray();
        if (($payload['template'] ?? null) !== ActivityTemplate::MatchWithLines->value) {
            throw ValidationException::withMessages([
                'template' => 'The activity must use the match_with_lines template.',
            ]);
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function findLeftItem(array $payload, string $leftItemId): array
    {
        foreach ($payload['items'] as $item) {
            if (($item['id'] ?? null) === $leftItemId) {
                return $item;
            }
        }

        throw ValidationException::withMessages([
            'left_item_id' => 'The left element does not exist.',
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return list<string>
     */
    private function rightItemIds(array $payload): array
    {
        return array_values(array_map(
            static fn (array $item): string => (string) $item['data']['right']['id'],
            $payload['items'],
        ));
    }
}
