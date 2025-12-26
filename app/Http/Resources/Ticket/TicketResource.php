<?php

namespace App\Http\Resources\Ticket;

use App\Enums\MediaCollectionEnum;
use App\Http\Resources\Public\Media\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'message' => $this->resource->message,
            'seen_at' => $this->resource->seen_at,
            'created_at' => $this->resource->created_at,

            'user' => [
                'full_name' => $this->resource->user->full_name,
                'avatar' => MediaResource::make(
                    $this->resource->user->getFirstMedia(MediaCollectionEnum::AVATAR->value)
                ),
            ],
            'admin' => [
                'full_name' => $this->resource->admin?->full_name,
                'avatar' => MediaResource::make($this->resource->admin?->getFirstMedia(MediaCollectionEnum::AVATAR->value)),
            ],

            'attachments' => MediaResource::collection($this->resource->getMedia(MediaCollectionEnum::TICKET->value)),

            'replies' => self::collection($this->resource->replies)
        ];
    }
}
