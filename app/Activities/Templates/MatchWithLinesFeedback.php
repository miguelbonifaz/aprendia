<?php

namespace App\Activities\Templates;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, mixed> */
final readonly class MatchWithLinesFeedback implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $leftItemId,
        public string $rightItemId,
        public bool $isCorrect,
        public string $message,
        public ?string $hint,
    ) {}

    /** @return array{left_item_id: string, right_item_id: string, is_correct: bool, message: string, hint: ?string} */
    public function toArray(): array
    {
        return [
            'left_item_id' => $this->leftItemId,
            'right_item_id' => $this->rightItemId,
            'is_correct' => $this->isCorrect,
            'message' => $this->message,
            'hint' => $this->hint,
        ];
    }

    /** @return array{left_item_id: string, right_item_id: string, is_correct: bool, message: string, hint: ?string} */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
