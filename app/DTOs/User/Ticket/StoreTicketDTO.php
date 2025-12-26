<?php

namespace App\DTOs\User\Ticket;

class StoreTicketDTO
{
    public function __construct(
        public readonly int    $user_id,
        public readonly string $subject,
        public readonly string $message,
        public readonly ?array $attachments
    )
    {}


    public static function fromRequest(array $data, int $userId): static
    {
        return new static(
            $userId,
            $data['subject'],
            $data['message'],
            $data['attachments'] ?? null
        );
    }
}
