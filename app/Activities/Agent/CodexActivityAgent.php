<?php

namespace App\Activities\Agent;

use App\Activities\ActivityDefinition;
use App\Models\Student;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Throwable;

final readonly class CodexActivityAgent implements ActivityAgent
{
    public function __construct(private ActivityAgentPrompt $prompt) {}

    public function generate(Student $student, array $messages): ActivityDefinition
    {
        $outputPath = tempnam(sys_get_temp_dir(), 'aprendia-agent-');
        $schemaPath = tempnam(sys_get_temp_dir(), 'aprendia-schema-');

        if ($outputPath === false || $schemaPath === false) {
            File::delete(array_filter([$outputPath, $schemaPath]));

            throw new ActivityAgentUnavailable('Unable to prepare Codex files.');
        }

        try {
            File::put($schemaPath, json_encode(
                ActivityContentSchema::get(),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            ));

            $result = Process::path(sys_get_temp_dir())
                ->timeout((int) config('activity_agent.codex.timeout'))
                ->input($this->prompt->build($student, $messages))
                ->run($this->command($outputPath, $schemaPath));

            if ($result->failed()) {
                throw new ActivityAgentUnavailable('Codex did not complete successfully.');
            }

            $reply = trim(File::get($outputPath));

            if ($reply === '') {
                throw new ActivityAgentUnavailable('Codex returned an empty response.');
            }

            $content = json_decode($reply, true, flags: JSON_THROW_ON_ERROR);

            if (! is_array($content)) {
                throw new ActivityAgentUnavailable('Codex returned an invalid activity.');
            }

            return GeneratedActivityDefinition::fromContent($student, $content);
        } catch (ActivityAgentUnavailable $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new ActivityAgentUnavailable('Codex is unavailable.', previous: $exception);
        } finally {
            File::delete([$outputPath, $schemaPath]);
        }
    }

    /** @return list<string> */
    private function command(string $outputPath, string $schemaPath): array
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
            '--output-schema',
            $schemaPath,
            '-',
        ];
    }
}
