<?php

namespace App\Http\Controllers;

use App\Activities\Agent\ActivityAgentUnavailable;
use App\Activities\Agent\ActivityConversation;
use App\Http\Requests\SendChatMessageRequest;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class ChatMessageController extends Controller
{
    public function __construct(private readonly ActivityConversation $conversation) {}

    public function __invoke(SendChatMessageRequest $request): JsonResponse
    {
        $student = $this->selectedStudent($request);

        if (! $student) {
            return response()->json([
                'message' => 'Selecciona un alumno válido antes de continuar.',
            ], 409);
        }

        Gate::authorize('view', $student);

        try {
            $reply = $this->conversation->reply(
                $request->session(),
                $student,
                $request->string('message')->trim()->toString(),
            );
        } catch (ActivityAgentUnavailable $exception) {
            report($exception);

            return response()->json([
                'message' => 'No pude responder en este momento. Inténtalo nuevamente.',
            ], 503);
        }

        return response()->json([
            'message' => ['role' => 'assistant', 'content' => $reply],
        ]);
    }

    private function selectedStudent(Request $request): ?Student
    {
        $selectedStudentId = $request->session()->get('selected_student_id');

        if (! $selectedStudentId) {
            return null;
        }

        $student = $request->user()?->students()->whereKey((int) $selectedStudentId)->first();

        if (! $student) {
            $request->session()->forget('selected_student_id');
        }

        return $student;
    }
}
