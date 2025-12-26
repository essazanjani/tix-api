<?php

namespace App\Http\Requests\User\Ticket;

use App\DTOs\User\Ticket\StoreTicketDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'attachments' => ['nullable', 'array', 'max:2'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048']
        ];
    }


    public function toDTO(int $userId): StoreTicketDTO
    {
        return StoreTicketDTO::fromRequest($this->validated(), $userId);
    }
}
