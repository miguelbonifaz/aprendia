<?php

namespace App\Activities\Agent;

use App\Models\Student;
use Illuminate\Session\Store;

final readonly class ActivityConversation
{
    private const int MAX_MESSAGES = 20;

    public function __construct(private ActivityAgent $agent) {}

    public function reply(Store $session, Student $student, string $message): string
    {
        $key = "activity_agent_conversations.{$student->id}";
        $history = $this->validHistory($session->get($key, []));
        $history = array_slice($history, -(self::MAX_MESSAGES - 2));
        $context = [...$history, ['role' => 'user', 'content' => $message]];
        $reply = $this->agent->respond($student, $context);

        $session->put($key, [
            ...$context,
            ['role' => 'assistant', 'content' => $reply],
        ]);

        return $reply;
    }

    /**
     * @return list<array{role: 'user'|'assistant', content: string}>
     */
    private function validHistory(mixed $history): array
    {
        if (! is_array($history)) {
            return [];
        }

        return array_values(array_filter(
            $history,
            static fn (mixed $message): bool => is_array($message)
                && in_array($message['role'] ?? null, ['user', 'assistant'], true)
                && is_string($message['content'] ?? null),
        ));
    }
}
