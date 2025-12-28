<?php

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use App\Enums\MediaCollectionEnum;


beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
    $this->admin = User::factory()->create();
    $this->ticket = Ticket::factory()->for($this->user)->create();
    $this->endpoint = route('user.ticket.show', $this->ticket);
});


test('returns ticket with replies and attachments and marks replies as seen', function () {
    $this->actingAs($this->user);

    $this->travelTo(now());

    $this->ticket->addMedia(UploadedFile::fake()->image('attachment.jpg'))
        ->toMediaCollection(MediaCollectionEnum::TICKET->value);

    $adminReply = Ticket::factory()->reply($this->ticket)->create(['admin_id' => $this->admin->id]);

    $response = $this->getJson($this->endpoint);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($response->json('data.id'))->toBe($this->ticket->id)
        ->and($response->json('data.attachments'))
        ->and($response->json('data.replies'))->toHaveCount(1)
        ->and($response->json('data.replies.0.admin.full_name'))->toBe($this->admin->full_name)
        ->and($adminReply->fresh()->seen_at->toDateTimeString())->toBe(now()->toDateTimeString());
});


test('user can not view someone else ticket', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson($this->endpoint);

    expect($response->assertForbidden())
        ->and($response->json('status'))->toBe('error');
});


test('fails if ticket is not parent', function () {
    $this->actingAs($this->user);

    $ticket = Ticket::factory()->reply($this->ticket)->create();

    $response = $this->getJson(route('user.ticket.show', $ticket));

    expect($response->assertBadRequest())
        ->and($response->json('status'))->toBe('error');
});
