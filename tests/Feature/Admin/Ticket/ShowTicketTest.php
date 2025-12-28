<?php

use App\Models\Ticket;
use App\Models\User;
use App\Enums\MediaCollectionEnum;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    Storage::fake('public');
    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo('TicketController@show');
    $this->actingAs($this->admin);
    $this->user = User::factory()->create();
    $this->ticket = Ticket::factory()->for($this->user)->create();
    $this->endpoint = route('admin.ticket.show', $this->ticket);
});


test('returns ticket with replies and attachments', function () {
    $this->ticket->addMedia(UploadedFile::fake()->image('attachment.jpg'))
        ->toMediaCollection(MediaCollectionEnum::TICKET->value);

    $userReply = Ticket::factory()->reply($this->ticket)->create();
    $adminReply = Ticket::factory()->reply($this->ticket)->create(['admin_id' => $this->admin->id]);

    $response = $this->getJson($this->endpoint);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($response->json('data.id'))->toBe($this->ticket->id)
        ->and($response->json('data.attachments'))->toBeArray()
        ->and($response->json('data.replies'))->toHaveCount(2);
});


test('marks user replies as seen', function () {
    $this->travelTo(now());

    $userReply = Ticket::factory()->reply($this->ticket)->create(['admin_id' => null, 'seen_at' => null]);
    $adminReply = Ticket::factory()->reply($this->ticket)->create(['admin_id' => $this->admin->id, 'seen_at' => null]);

    $this->getJson($this->endpoint);

    expect($userReply->fresh()->seen_at->toDateTimeString())->toBe(now()->toDateTimeString())
        ->and($adminReply->fresh()->seen_at)->toBeNull();
});


test('fails if ticket is not parent', function () {
    $reply = Ticket::factory()->reply($this->ticket)->create();

    $response = $this->getJson(route('admin.ticket.show', $reply));

    expect($response->assertBadRequest())
        ->and($response->json('status'))->toBe('error');
});


test('fails without permission', function () {
    $admin = User::factory()->create();
    $this->actingAs($admin);

    $response = $this->getJson($this->endpoint);

    expect($response->assertForbidden());
});
