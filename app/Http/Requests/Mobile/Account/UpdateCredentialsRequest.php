<?php

namespace App\Http\Requests\Mobile\Account;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCredentialsRequest extends FormRequest
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
            'prev_password' => ['required', 'string', 'current_password:sanctum'],
            'new_password' => ['required', 'string', 'min:8', 'same:password_confirmation'],
            'password_confirmation' => ['required'],
        ];
    }}