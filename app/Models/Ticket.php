<?php

namespace App\Models;

use App\Enums\MediaCollectionEnum;
use App\Policies\TicketPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[UsePolicy(TicketPolicy::class)]
class Ticket extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'admin_id',
        'parent_id',
        'subject',
        'message',
        'status',
        'seen_at'
    ];

    protected function casts(): array
    {
        return [
            'seen_at' => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }


    public function parent(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'parent_id');
    }


    public function replies(): HasMany
    {
        return $this->hasMany(Ticket::class, 'parent_id');
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionEnum::TICKET->value)->onlyKeepLatest(2);
    }
}
