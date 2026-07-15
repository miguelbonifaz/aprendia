<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Student::class);

        $students = $request->user()->students()->latest()->get();
        $selectedStudent = $students->firstWhere('id', (int) $request->session()->get('selected_student_id'));

        if ($request->session()->has('selected_student_id') && ! $selectedStudent) {
            $request->session()->forget('selected_student_id');
        }

        return Inertia::render('students/index', [
            'students' => $students->map(fn (Student $student): array => $this->studentPayload($student)),
            'selectedStudent' => $selectedStudent ? $this->studentPayload($selectedStudent) : null,
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $request->user()->students()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Alumno registrado correctamente.']);

        return back();
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $student->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Datos del alumno actualizados.']);

        return back();
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
