<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Student::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', Rule::date()->todayOrBefore()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ingresa el nombre del alumno.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'birth_date.required' => 'Ingresa la fecha de nacimiento.',
            'birth_date.date' => 'Ingresa una fecha de nacimiento válida.',
            'birth_date.before_or_equal' => 'La fecha de nacimiento no puede ser posterior a hoy.',
        ];
    }
}
