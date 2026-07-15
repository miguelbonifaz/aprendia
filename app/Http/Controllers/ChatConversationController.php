<?php

namespace App\Http\Controllers;

use App\Activities\Agent\ActivityConversation;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChatConversationController extends Controller
{
    public function __construct(private readonly ActivityConversation $conversation) {}

    public function __invoke(Request $request): JsonResponse
    {
        $student = $this->selectedStudent($request);

        if (! $student) {
            return response()->json([
                'message' => 'Selecciona un alumno válido antes de continuar.',
            ], 409);
        }

        Gate::authorize('view', $student);
        $this->conversation->clear($request->session(), $student);

        return response()->json(['messages' => []]);
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
