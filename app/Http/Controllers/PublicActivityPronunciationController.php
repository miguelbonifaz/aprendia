<?php

namespace App\Http\Controllers;

use App\Activities\Agent\ActivityAgentUnavailable;
use App\Activities\Agent\OpenAIActivitySpeech;
use App\Activities\PlayableActivity;
use App\Models\Activity;
use Illuminate\Http\Response;

final class PublicActivityPronunciationController extends Controller
{
    public function __invoke(Activity $activity, string $itemId, OpenAIActivitySpeech $speech): Response
    {
        $mediaId = "{$itemId}_pronunciation";
        $media = $activity->mediaAssets()->where('media_id', $mediaId)->first();

        if ($media === null) {
            try {
                $spokenWord = PlayableActivity::from($activity)->spokenWordFor($itemId);
                $content = $speech->generate($spokenWord);
                $media = $activity->mediaAssets()->firstOrCreate(
                    ['media_id' => $mediaId],
                    ['mime_type' => 'audio/mpeg', 'content' => base64_encode($content)],
                );
            } catch (ActivityAgentUnavailable) {
                return response('No pudimos generar el audio. Inténtalo nuevamente.', 503);
            }
        }

        $content = base64_decode($media->content, true);
        abort_if($content === false, 404);

        return response($content, 200, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Content-Length' => (string) strlen($content),
            'Content-Type' => 'audio/mpeg',
        ]);
    }
}
