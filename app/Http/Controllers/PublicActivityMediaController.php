<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Response;

final class PublicActivityMediaController extends Controller
{
    public function __invoke(Activity $activity, string $mediaId): Response
    {
        $media = $activity->mediaAssets()->where('media_id', $mediaId)->firstOrFail();
        $content = base64_decode($media->content, true);

        abort_if($content === false, 404);

        return response($content, 200, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Content-Length' => (string) strlen($content),
            'Content-Type' => $media->mime_type,
        ]);
    }
}
