<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $selectedStudentId = $request->session()->get('selected_student_id');

        if (! $selectedStudentId) {
            return $this->redirectToStudents('Selecciona un alumno para continuar al chat.');
        }

        $student = $request->user()->students()->whereKey((int) $selectedStudentId)->first();

        if (! $student) {
            $request->session()->forget('selected_student_id');

            return $this->redirectToStudents('La selección ya no está disponible. Elige otro alumno.');
        }

        return Inertia::render('chat/index', [
            'student' => $this->studentPayload($student),
        ]);
    }

    private function redirectToStudents(string $message): RedirectResponse
    {
        Inertia::flash('toast', ['type' => 'info', 'message' => $message]);

        return redirect()->route('students.index');
    }

    /**
     * @return array{id: int, name: string, birth_date: string, age: int}
     */
    private function studentPayload(Student $student): array
    {
        return [
            'id' => $student->id,
            'name' => $student->name,
            'birth_date' => $student->birth_date->toDateString(),
            'age' => $student->age,
        ];
    }
}
