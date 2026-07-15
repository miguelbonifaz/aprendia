<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class EvaluateActivityResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array', 'min:1', 'max:50'],
            'answers.*' => ['required', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'answers.required' => 'Responde todas las preguntas antes de terminar.',
            'answers.array' => 'Las respuestas enviadas no son válidas.',
        ];
    }
}
