<?php

namespace App\Services\User;

use App\DTOs\User\Ticket\ReplyTicketDTO;
use App\DTOs\User\Ticket\StoreTicketDTO;
use App\Enums\MediaCollectionEnum;
use App\Enums\TicketStatusEnum;
use App\Exceptions\CustomException;
use App\Models\Ticket;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function index(int $userId): LengthAwarePaginator
    {
        return Ticket::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }


    public function show(Ticket $ticket): Ticket
    {
        if ($ticket->parent_id){
            throw new CustomException();
        }

        Ticket::query()
            ->where('parent_id', $ticket->id)
            ->whereNotNull('admin_id')
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        return $ticket->load(['user.media', 'admin.media', 'media', 'replies.user', 'replies.admin', 'replies.media']);
    }


    public function store(StoreTicketDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $ticket = Ticket::query()->create([
                'user_id' => $dto->user_id,
                'subject' => $dto->subject,
                'message' => $dto->message
            ]);

            $this->uploadAttachments($ticket, $dto->attachments);
        });
    }


    public function reply(ReplyTicketDTO $dto, Ticket $ticket): void
    {
        if ($ticket->parent_id) {
            throw new CustomException();
        }

        if ($ticket->status === TicketStatusEnum::CLOSED->value) {
            throw new CustomException(trans('errors.ticket_closed'));
        }

        Ticket::query()->create([
            'user_id' => $ticket->user_id,
            'parent_id' => $ticket->id,
            'subject' => $ticket->subject,
            'message' => $dto->message,
            'status' => TicketStatusEnum::PENDING->value
        ]);
    }


    private function uploadAttachments(Ticket $ticket, array $files): void
    {
        foreach ($files as $file) {
            $ticket->addMedia($file)
                ->toMediaCollection(MediaCollectionEnum::TICKET->value);
        }
    }
}
