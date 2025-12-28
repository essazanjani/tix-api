<?php

namespace App\DTOs\Admin\Ticket;

class ReplyTicketDTO
{
    public function __construct(
        public readonly int $admin_id,
        public readonly string $message,
        public readonly array $attachments = []
    ) {}


    public static function fromRequest(array $data, int $adminId): static
    {
        return new static(
            admin_id: $adminId,
            message: $data['message'],
            attachments: $data['attachments'] ?? []
        );
    }
}
