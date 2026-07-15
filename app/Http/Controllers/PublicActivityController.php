<?php

namespace App\Http\Controllers;

use App\Activities\ActivityDefinition;
use App\Models\Activity;
use Inertia\Inertia;
use Inertia\Response;

final class PublicActivityController extends Controller
{
    public function __invoke(Activity $activity): Response
    {
        $definition = ActivityDefinition::fromArray($activity->definition)->toArray();

        return Inertia::render('activities/show', [
            'activity' => [
                'title' => $definition['title'],
                'instructions' => $definition['instructions'],
                'learning_objective' => $definition['learning_objective'],
            ],
        ]);
    }
}
