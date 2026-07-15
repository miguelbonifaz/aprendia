<?php

namespace App\Activities\Templates;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, mixed> */
final readonly class ListenReadAndRespondFeedback implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $itemId,
        public string $selectedOptionId,
        public bool $isCorrect,
        public string $message,
        public ?string $hint,
    ) {}

    /** @return array{item_id: string, selected_option_id: string, is_correct: bool, message: string, hint: ?string} */
    public function toArray(): array
    {
        return [
            'item_id' => $this->itemId,
            'selected_option_id' => $this->selectedOptionId,
            'is_correct' => $this->isCorrect,
            'message' => $this->message,
            'hint' => $this->hint,
        ];
    }

    /** @return array{item_id: string, selected_option_id: string, is_correct: bool, message: string, hint: ?string} */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
