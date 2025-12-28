<?php

namespace App\Services\Admin;

use App\DTOs\Admin\Ticket\ChangeStatusDTO;
use App\DTOs\Admin\Ticket\ReplyTicketDTO;
use App\Enums\MediaCollectionEnum;
use App\Enums\TicketStatusEnum;
use App\Exceptions\CustomException;
use App\Models\Ticket;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService
{
    public function index(): LengthAwarePaginator
    {
        return Ticket::query()
            ->with('user')
            ->whereNull('parent_id')
            ->withCount(['replies as unseen_replies_count' => function ($query) {
                $query->whereNull('admin_id')
                    ->whereNull('seen_at');
            }])
            ->latest()
            ->paginate(10);
    }


    public function show(Ticket $ticket): Ticket
    {
        if ($ticket->parent_id) {
            throw new CustomException();
        }

        Ticket::query()
            ->where('parent_id', $ticket->id)
            ->whereNull('admin_id')
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        return $ticket->load(['user.media', 'admin.media', 'media', 'replies.user', 'replies.admin', 'replies.media']);
    }


    public function reply(ReplyTicketDTO $dto, Ticket $ticket): void
    {
        if ($ticket->parent_id) {
            throw new CustomException();
        }

        if ($ticket->status === TicketStatusEnum::CLOSED->value) {
            throw new CustomException(trans('base.errors.ticket_closed'));
        }

        $reply = Ticket::query()->create([
            'user_id' => $ticket->user_id,
            'admin_id' => $dto->admin_id,
            'parent_id' => $ticket->id,
            'subject' => $ticket->subject,
            'message' => $dto->message,
            'status' => TicketStatusEnum::OPEN->value
        ]);

        $this->uploadAttachments($reply, $dto->attachments);
    }


    public function changeStatus(ChangeStatusDTO $dto, Ticket $ticket): void
    {
        if ($ticket->parent_id) {
            throw new CustomException();
        }

        $ticket->update(['status' => $dto->status]);
    }


    private function uploadAttachments(Ticket $ticket, array $files): void
    {
        foreach ($files as $file) {
            $ticket->addMedia($file)
                ->toMediaCollection(MediaCollectionEnum::TICKET->value);
        }
    }
}
