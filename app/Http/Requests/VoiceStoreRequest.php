<?php

namespace App\Http\Requests;

use App\Traits\ThrowValidationException;
use Illuminate\Foundation\Http\FormRequest;

class VoiceStoreRequest extends FormRequest
{
    use ThrowValidationException;
    
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
            'user_id' => 'required|int|exists:users,id',
            'question_id' => 'required|int|exists:questions,id',
            'value' => 'required|boolean'
        ];
    }
}
