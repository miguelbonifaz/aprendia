<?php

namespace App\Activities\Agent;

use App\Activities\ActivityDefinition;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

final readonly class OpenAIActivityImages
{
    /** @return list<array{media_id: string, mime_type: string, content: string}> */
    public function generate(ActivityDefinition $definition): array
    {
        $apiKey = (string) config('activity_agent.openai.api_key');
        $media = array_values(array_filter(
            $definition->toArray()['media'],
            static fn (array $item): bool => ($item['type'] ?? null) === 'image',
        ));

        if ($apiKey === '' || $media === []) {
            throw new ActivityAgentUnavailable('Activity images cannot be generated.');
        }

        try {
            $responses = Http::pool(
                fn (Pool $pool): array => $this->requests($pool, $media, $apiKey),
                concurrency: 3,
            );

            return array_map(
                fn (array $item): array => $this->mediaRecord($item, $responses[(string) $item['id']] ?? null),
                $media,
            );
        } catch (ActivityAgentUnavailable $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new ActivityAgentUnavailable('OpenAI images are unavailable.', previous: $exception);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $media
     * @return array<string, mixed>
     */
    private function requests(Pool $pool, array $media, string $apiKey): array
    {
        $requests = [];

        foreach ($media as $item) {
            $mediaId = (string) $item['id'];
            $requests[$mediaId] = $pool->as($mediaId)
                ->withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->connectTimeout((int) config('activity_agent.openai.connect_timeout'))
                ->timeout((int) config('activity_agent.openai.image_timeout'))
                ->post($this->endpoint(), $this->payload((string) $item['alt_text']));
        }

        return $requests;
    }

    /** @return array<string, mixed> */
    private function payload(string $description): array
    {
        return [
            'model' => config('activity_agent.openai.image_model'),
            'prompt' => "Ilustración educativa infantil, colorida, amable y clara, fondo sencillo, un solo objeto protagonista. Sin texto, letras, números, marcas ni logotipos. {$description}",
            'size' => config('activity_agent.openai.image_size'),
            'quality' => config('activity_agent.openai.image_quality'),
            'output_format' => 'webp',
            'output_compression' => config('activity_agent.openai.image_compression'),
        ];
    }

    /** @return array{media_id: string, mime_type: string, content: string} */
    private function mediaRecord(array $item, mixed $response): array
    {
        if (! $response instanceof Response || $response->failed()) {
            throw new ActivityAgentUnavailable('OpenAI did not generate an activity image.');
        }

        $content = $response->json('data.0.b64_json');

        if (! is_string($content) || base64_decode($content, true) === false) {
            throw new ActivityAgentUnavailable('OpenAI returned an invalid activity image.');
        }

        return [
            'media_id' => (string) $item['id'],
            'mime_type' => 'image/webp',
            'content' => $content,
        ];
    }

    private function endpoint(): string
    {
        return rtrim((string) config('activity_agent.openai.base_url'), '/').'/images/generations';
    }
}
