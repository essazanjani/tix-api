<?php

use App\Models\Ticket;
use App\Models\User;
use App\Enums\TicketStatusEnum;
use App\Enums\MediaCollectionEnum;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    Storage::fake('public');
    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo('TicketController@reply');
    $this->actingAs($this->admin);
    $this->user = User::factory()->create();
    $this->ticket = Ticket::factory()->for($this->user)->create();
    $this->endpoint = route('admin.ticket.reply', $this->ticket);
});


test('admin can reply to a ticket', function () {
    $response = $this->postJson($this->endpoint, [
        'message' => 'Admin reply message'
    ]);

    expect($response->assertCreated())
        ->and($response->json('status'))->toBe('success')
        ->and($this->ticket->replies)->toHaveCount(1)
        ->and($this->ticket->replies->first()->admin_id)->toBe($this->admin->id)
        ->and($this->ticket->replies->first()->status)->toBe(TicketStatusEnum::OPEN->value);
});


test('admin can reply with attachments', function () {
    $response = $this->postJson($this->endpoint, [
        'message' => 'Admin reply with attachment',
        'attachments' => [
            UploadedFile::fake()->image('attachment.jpg')
        ]
    ]);

    $reply = $this->ticket->replies->first();

    expect($response->assertCreated())
        ->and($reply->getMedia(MediaCollectionEnum::TICKET->value))->toHaveCount(1);
});


test('fails if ticket is not parent', function () {
    $reply = Ticket::factory()->reply($this->ticket)->create();

    $response = $this->postJson(route('admin.ticket.reply', $reply), [
        'message' => 'Test message'
    ]);

    expect($response->assertBadRequest())
        ->and($response->json('status'))->toBe('error');
});


test('cannot reply to a closed ticket', function () {
    $this->ticket->update(['status' => TicketStatusEnum::CLOSED->value]);

    $response = $this->postJson($this->endpoint, [
        'message' => 'Reply to closed ticket'
    ]);

    expect($response->assertBadRequest())
        ->and($response->json('status'))->toBe('error');
});


test('fails without permission', function () {
    $admin = User::factory()->create();
    $this->actingAs($admin);

    $response = $this->postJson($this->endpoint, [
        'message' => 'Test message'
    ]);

    expect($response->assertForbidden());
});


test('fails with invalid data', function () {
    $response = $this->postJson($this->endpoint, []);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error');
});
