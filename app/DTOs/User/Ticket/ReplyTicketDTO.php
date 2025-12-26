<?php

namespace App\DTOs\User\Ticket;

class ReplyTicketDTO
{
    public function __construct(public readonly string $message)
    {}


    public static function fromRequest(array $data): static
    {
        return new static($data['message']);
    }
}
