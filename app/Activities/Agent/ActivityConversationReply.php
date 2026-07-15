<?php

namespace App\Activities\Agent;

use App\Models\Activity;

final readonly class ActivityConversationReply
{
    public function __construct(public string $content, public Activity $activity) {}

    /**
     * @return array{
     *     role: 'assistant',
     *     content: string,
     *     activity: array{public_id: string, title: string, url: string}
     * }
     */
    public function toMessage(): array
    {
        return [
            'role' => 'assistant',
            'content' => $this->content,
            'activity' => [
                'public_id' => $this->activity->public_id,
                'title' => (string) $this->activity->definition['title'],
                'url' => route('activities.show', $this->activity),
            ],
        ];
    }
}
