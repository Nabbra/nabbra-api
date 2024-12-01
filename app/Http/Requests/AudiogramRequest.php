<?php

namespace App\Http\Requests;

use App\Rules\AudiogramFrequenciesRule;
use Illuminate\Foundation\Http\FormRequest;

class AudiogramRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'right_ear' => ['required', 'array', new AudiogramFrequenciesRule()],
            'left_ear' => ['required', 'array', new AudiogramFrequenciesRule()],
        ];
    }
}
