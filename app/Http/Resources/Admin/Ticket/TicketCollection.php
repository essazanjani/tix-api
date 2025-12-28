<?php

namespace App\Http\Resources\Admin\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TicketCollection extends ResourceCollection
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'subject' => $item->subject,
                'status' => $item->status,
                'unseen_replies_count' => $item->unseen_replies_count ?? 0,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'user_full_name' => $item->user->full_name,
            ];
        })->toArray();
    }
}
