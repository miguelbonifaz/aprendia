<?php

namespace App\Activities\Agent;

use App\Models\Student;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final readonly class ActivityConversation
{
    private const int MAX_MESSAGES = 20;

    public function __construct(private ActivityAgent $agent) {}

    public function reply(Store $session, Student $student, string $message): ActivityConversationReply
    {
        $key = $this->sessionKey($student);
        $history = $this->history($session, $student);
        $history = array_slice($history, -(self::MAX_MESSAGES - 2));
        $context = [...$history, ['role' => 'user', 'content' => $message]];
        $definition = $this->agent->generate($student, $context);

        try {
            return DB::transaction(function () use ($session, $student, $definition, $context, $key): ActivityConversationReply {
                $activity = $student->activities()->create([
                    'public_id' => (string) Str::uuid(),
                    'definition' => $definition->toArray(),
                ]);
                $reply = new ActivityConversationReply(
                    content: "He creado una actividad para {$student->name}.",
                    activity: $activity,
                );

                $session->put($key, [...$context, $reply->toMessage()]);

                return $reply;
            });
        } catch (Throwable $exception) {
            throw new ActivityAgentUnavailable('Unable to save the generated activity.', previous: $exception);
        }
    }

    /**
     * @return list<array{
     *     role: 'user'|'assistant',
     *     content: string,
     *     activity?: array{public_id: string, title: string, url: string}
     * }>
     */
    public function history(Store $session, Student $student): array
    {
        return array_slice(
            $this->validHistory($session->get($this->sessionKey($student), [])),
            -self::MAX_MESSAGES,
        );
    }

    public function clear(Store $session, Student $student): void
    {
        $session->forget($this->sessionKey($student));
    }

    private function sessionKey(Student $student): string
    {
        return "activity_agent_conversations.{$student->id}";
    }

    /**
     * @return list<array{
     *     role: 'user'|'assistant',
     *     content: string,
     *     activity?: array{public_id: string, title: string, url: string}
     * }>
     */
    private function validHistory(mixed $history): array
    {
        if (! is_array($history)) {
            return [];
        }

        $messages = [];

        foreach ($history as $message) {
            if (! is_array($message)
                || ! in_array($message['role'] ?? null, ['user', 'assistant'], true)
                || ! is_string($message['content'] ?? null)) {
                continue;
            }

            $validMessage = [
                'role' => $message['role'],
                'content' => $message['content'],
            ];

            if ($this->hasValidActivity($message['activity'] ?? null)) {
                $validMessage['activity'] = $message['activity'];
            }

            $messages[] = $validMessage;
        }

        return $messages;
    }

    private function hasValidActivity(mixed $activity): bool
    {
        return is_array($activity)
            && is_string($activity['public_id'] ?? null)
            && is_string($activity['title'] ?? null)
            && is_string($activity['url'] ?? null);
    }
}
