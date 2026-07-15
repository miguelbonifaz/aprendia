<?php

namespace App\Http\Controllers;

use App\Activities\PlayableActivity;
use App\Models\Activity;
use Inertia\Inertia;
use Inertia\Response;

final class PublicActivityController extends Controller
{
    public function __invoke(Activity $activity): Response
    {
        return Inertia::render('activities/show', [
            'activity' => PlayableActivity::from($activity)->toPublicArray(),
        ]);
    }
}
