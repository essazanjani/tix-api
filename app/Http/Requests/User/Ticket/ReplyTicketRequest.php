<?php

namespace App\Http\Requests\User\Ticket;

use App\DTOs\User\Ticket\ReplyTicketDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReplyTicketRequest extends FormRequest
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
            'message' => ['required', 'string']
        ];
    }


    public function toDTO(): ReplyTicketDTO
    {
        return ReplyTicketDTO::fromRequest($this->validated());
    }
}
