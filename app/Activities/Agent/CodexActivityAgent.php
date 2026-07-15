<?php

namespace App\Activities\Agent;

use App\Models\Student;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Throwable;

final readonly class CodexActivityAgent implements ActivityAgent
{
    public function __construct(private ActivityAgentPrompt $prompt) {}

    public function respond(Student $student, array $messages): string
    {
        $outputPath = tempnam(sys_get_temp_dir(), 'aprendia-agent-');

        if ($outputPath === false) {
            throw new ActivityAgentUnavailable('Unable to prepare the Codex output file.');
        }

        try {
            $result = Process::path(sys_get_temp_dir())
                ->timeout((int) config('activity_agent.codex.timeout'))
                ->input($this->prompt->build($student, $messages))
                ->run($this->command($outputPath));

            if ($result->failed()) {
                throw new ActivityAgentUnavailable('Codex did not complete successfully.');
            }

            $reply = trim(File::get($outputPath));

            if ($reply === '') {
                throw new ActivityAgentUnavailable('Codex returned an empty response.');
            }

            return $reply;
        } catch (ActivityAgentUnavailable $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new ActivityAgentUnavailable('Codex is unavailable.', previous: $exception);
        } finally {
            File::delete($outputPath);
        }
    }

    /** @return list<string> */
    private function command(string $outputPath): array
    {
        return [
            (string) config('activity_agent.codex.binary'),
            'exec',
            '--ephemeral',
            '--ignore-user-config',
            '--ignore-rules',
            '--skip-git-repo-check',
            '--sandbox',
            'read-only',
            '--color',
            'never',
            '--output-last-message',
            $outputPath,
            '-',
        ];
    }
}
