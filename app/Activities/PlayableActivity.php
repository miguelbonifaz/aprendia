<?php

namespace App\Activities;

use App\Activities\Templates\RecognizeAndSelectEvaluator;
use App\Activities\Templates\RecognizeAndSelectFeedback;
use App\Models\Activity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class PlayableActivity
{
    /** @param array<string, mixed> $payload */
    private function __construct(
        private ActivityDefinition $definition,
        private array $payload,
        private string $publicId,
    ) {}

    public static function from(Activity $activity): self
    {
        try {
            $definition = ActivityDefinition::fromArray($activity->definition);
        } catch (ValidationException) {
            self::notFound();
        }

        $payload = $definition->toArray();

        if (! self::supports($payload)) {
            self::notFound();
        }

        return new self($definition, $payload, $activity->public_id);
    }

    /**
     * @return array{
     *     public_id: string,
     *     student_name: string,
     *     title: string,
     *     instructions: string,
     *     learning_objective: string,
     *     items: list<array{id: string, prompt: string, image_url: ?string, image_alt_text: ?string, options: list<array{id: string, text: string}>}>
     * }
     */
    public function toPublicArray(): array
    {
        $media = collect($this->payload['media'])->keyBy('id')->all();

        return [
            'public_id' => $this->publicId,
            'student_name' => (string) $this->payload['student']['name'],
            'title' => (string) $this->payload['title'],
            'instructions' => (string) $this->payload['instructions'],
            'learning_objective' => (string) $this->payload['learning_objective'],
            'items' => array_map(
                fn (array $item): array => $this->publicItem($item, $media),
                $this->payload['items'],
            ),
        ];
    }

    public function evaluateAnswer(string $itemId, string $selectedOptionId): RecognizeAndSelectFeedback
    {
        return (new RecognizeAndSelectEvaluator)->evaluateAnswer(
            $this->definition,
            $itemId,
            $selectedOptionId,
        );
    }

    /** @param array<string, string> $answers */
    public function evaluateResult(array $answers): ActivityResult
    {
        return (new RecognizeAndSelectEvaluator)->evaluateResult($this->definition, $answers);
    }

    /** @param array<string, mixed> $payload */
    private static function supports(array $payload): bool
    {
        if (($payload['template'] ?? null) !== ActivityTemplate::RecognizeAndSelect->value) {
            return false;
        }

        foreach ($payload['items'] as $item) {
            if (($item['data']['prompt']['type'] ?? null) !== 'text') {
                return false;
            }

            foreach ($item['data']['options'] as $option) {
                if (($option['content']['type'] ?? null) !== 'text') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, array<string, mixed>>  $media
     * @return array<string, mixed>
     */
    private function publicItem(array $item, array $media): array
    {
        $mediaId = $item['data']['illustration_media_id'] ?? null;
        $illustration = is_string($mediaId) ? ($media[$mediaId] ?? null) : null;

        return [
            'id' => (string) $item['id'],
            'prompt' => (string) $item['data']['prompt']['text'],
            'image_url' => is_array($illustration)
                ? route('activities.media.show', [$this->publicId, $mediaId], absolute: false)
                : null,
            'image_alt_text' => is_array($illustration) ? (string) $illustration['alt_text'] : null,
            'options' => array_map(
                static fn (array $option): array => [
                    'id' => (string) $option['id'],
                    'text' => (string) $option['content']['text'],
                ],
                $item['data']['options'],
            ),
        ];
    }

    private static function notFound(): never
    {
        $exception = new ModelNotFoundException;
        $exception->setModel(Activity::class);

        throw $exception;
    }
}
