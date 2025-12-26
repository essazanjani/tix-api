<?php

namespace App\Http\Requests\Public\Auth;

use App\DTOs\Public\Auth\VerifyCodeDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyCodeRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', Rule::exists('users', 'email')],
            'code' => ['required', 'string', 'digits:5']
        ];
    }


    public function toDTO(): VerifyCodeDTO
    {
        return VerifyCodeDTO::fromRequest($this->validated());
    }
}
