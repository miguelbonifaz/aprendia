<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class EvaluateActivityAnswerRequest extends FormRequest
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
            'item_id' => ['required', 'string', 'max:255'],
            'selected_option_id' => ['required', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'item_id.required' => 'Selecciona una pregunta válida.',
            'selected_option_id.required' => 'Selecciona una respuesta.',
        ];
    }
}
