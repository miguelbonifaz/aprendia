<?php

namespace App\Http\Controllers;

use App\Activities\PlayableActivity;
use App\Http\Requests\EvaluateActivityAnswerRequest;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;

final class PublicActivityAnswerController extends Controller
{
    public function __invoke(EvaluateActivityAnswerRequest $request, Activity $activity): JsonResponse
    {
        $feedback = PlayableActivity::from($activity)->evaluateAnswer(
            $request->string('item_id')->toString(),
            $request->string('selected_option_id')->toString(),
        );

        return response()->json(['feedback' => $feedback->toArray()]);
    }
}
