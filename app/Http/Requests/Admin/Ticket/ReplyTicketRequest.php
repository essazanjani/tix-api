<?php

namespace App\Http\Requests\Admin\Ticket;

use App\DTOs\Admin\Ticket\ReplyTicketDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReplyTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240']
        ];
    }


    public function toDTO(int $adminId): ReplyTicketDTO
    {
        return ReplyTicketDTO::fromRequest($this->validated(), $adminId);
    }
}
