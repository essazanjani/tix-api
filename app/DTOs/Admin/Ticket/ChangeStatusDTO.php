<?php

namespace App\DTOs\Admin\Ticket;

use App\Enums\TicketStatusEnum;

class ChangeStatusDTO
{
    public function __construct(public readonly TicketStatusEnum $status)
    {}


    public static function fromRequest(array $data): static
    {
        return new static($data['status']);
    }
}
