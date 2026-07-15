<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudentSelectionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Student $student): RedirectResponse
    {
        Gate::authorize('view', $student);

        $request->session()->put('selected_student_id', $student->id);

        return redirect()->route('chat.index');
    }
}
