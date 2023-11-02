<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BattleshipRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'battleship_id' => 'required',
            'field' => ['required', 'regex:/^[A-J](10|[1-9])$/'],
            'rotation' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'field.regex' => 'Field has to contain big letter from range A-J and number from range 1-10',
        ];
    }
}
