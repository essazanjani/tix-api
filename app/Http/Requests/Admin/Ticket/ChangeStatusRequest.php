<?php

namespace App\Http\Requests\Admin\Ticket;

use App\DTOs\Admin\Ticket\ChangeStatusDTO;
use App\Enums\TicketStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusRequest extends FormRequest
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
            'status' => ['required', Rule::in(array_column(TicketStatusEnum::cases(), 'value'))]
        ];
    }


    public function toDTO(): ChangeStatusDTO
    {
        return ChangeStatusDTO::fromRequest($this->validated());
    }
}
