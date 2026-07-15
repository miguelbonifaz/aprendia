<?php

namespace App\Activities\Templates;

use App\Activities\ActivityContentType;
use Illuminate\Validation\Rule;

final class ActivityContentValidator
{
    /** @return array<string, array<int, mixed>> */
    public static function rules(string $path): array
    {
        return [
            $path => ['required', 'array:type,text,media_id'],
            "{$path}.type" => ['required', Rule::enum(ActivityContentType::class)],
            "{$path}.text" => ['sometimes', 'string', 'max:5000'],
            "{$path}.media_id" => ['sometimes', 'string', 'max:100'],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, array<string, mixed>>
     */
    public static function mediaById(array $payload): array
    {
        $mediaById = [];
        foreach ($payload['media'] as $media) {
            $mediaById[(string) $media['id']] = $media;
        }

        return $mediaById;
    }

    /**
     * @param  array<string, mixed>  $content
     * @param  array<string, array<string, mixed>>  $media
     * @param  array<string, list<string>>  $errors
     */
    public static function validate(array $content, string $path, array $media, array &$errors): void
    {
        $type = ActivityContentType::from((string) $content['type']);
        if ($type === ActivityContentType::Text) {
            if (! is_string($content['text'] ?? null) || trim($content['text']) === '') {
                $errors["{$path}.text"][] = 'Text content must provide text.';
            }
            if (array_key_exists('media_id', $content)) {
                $errors["{$path}.media_id"][] = 'Text content cannot reference media.';
            }

            return;
        }

        if (array_key_exists('text', $content)) {
            $errors["{$path}.text"][] = 'Media content cannot provide text.';
        }

        $mediaId = $content['media_id'] ?? null;
        if (! is_string($mediaId) || ! isset($media[$mediaId])) {
            $errors["{$path}.media_id"][] = 'The referenced media does not exist.';

            return;
        }

        $referencedMedia = $media[$mediaId];
        if (($referencedMedia['type'] ?? null) !== $type->value) {
            $errors["{$path}.media_id"][] = 'The referenced media type does not match the content.';
        }

        $accessibilityKey = $type === ActivityContentType::Image ? 'alt_text' : 'transcript';
        if (! is_string($referencedMedia[$accessibilityKey] ?? null) || trim($referencedMedia[$accessibilityKey]) === '') {
            $errors["media.{$mediaId}.{$accessibilityKey}"][] = "The referenced media requires {$accessibilityKey}.";
        }
    }
}
