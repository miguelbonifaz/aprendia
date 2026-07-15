<?php

namespace App\Activities;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, mixed> */
final readonly class ActivityResult implements Arrayable, JsonSerializable
{
    /** @param list<string> $recommendations */
    public function __construct(
        public int $score,
        public string $summary,
        public array $recommendations,
    ) {}

    /** @return array{score: int, summary: string, recommendations: list<string>} */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'summary' => $this->summary,
            'recommendations' => $this->recommendations,
        ];
    }

    /** @return array{score: int, summary: string, recommendations: list<string>} */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
