<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        Gate::authorize('viewAny', Student::class);

        $students = $request->user()->students()->latest()->get();
        $selectedStudent = $students->firstWhere('id', (int) $request->session()->get('selected_student_id'));

        if ($request->session()->has('selected_student_id') && ! $selectedStudent) {
            $request->session()->forget('selected_student_id');
        }

        return Inertia::render('dashboard', [
            'students' => $students->map(fn (Student $student): array => $this->studentPayload($student)),
            'selectedStudent' => $selectedStudent ? $this->studentPayload($selectedStudent) : null,
        ]);
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
