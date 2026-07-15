<?php

namespace App\Http\Controllers;

use App\Activities\PlayableActivity;
use App\Http\Requests\EvaluateActivityResultRequest;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;

final class PublicActivityResultController extends Controller
{
    public function __invoke(EvaluateActivityResultRequest $request, Activity $activity): JsonResponse
    {
        /** @var array<string, string> $answers */
        $answers = $request->validated('answers');
        $result = PlayableActivity::from($activity)->evaluateResult($answers);

        return response()->json([
            'result' => [
                'score' => $result->score,
                'summary' => $result->summary,
            ],
        ]);
    }
}
