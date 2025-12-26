<?php

use App\Models\User;
use App\Models\Ticket;
use App\Enums\TicketStatusEnum;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->ticket = Ticket::factory()->for($this->user)->create();
    $this->endpoint = route('ticket.reply', $this->ticket);
});


test('user can reply to a parent ticket', function () {
    $this->actingAs($this->user);

    $response = $this->postJson($this->endpoint, [
        'message' => 'Test message'
    ]);

    expect($response->assertCreated())
        ->and($response->json('status'))->toBe('success')
        ->and($this->ticket->replies)->toHaveCount(1);
});


test('user can not reply someone else ticket', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson($this->endpoint);

    expect($response->assertForbidden())
        ->and($response->json('status'))->toBe('error');
});


test('fails if ticket is not parent', function () {
    $this->actingAs($this->user);

    $ticket = Ticket::factory()->reply($this->ticket)->create();

    $response = $this->getJson(route('ticket.show', $ticket));

    expect($response->assertBadRequest())
        ->and($response->json('status'))->toBe('error');
});


test('cannot reply to a closed ticket', function () {
    $this->actingAs($this->user);

    $this->ticket->update(['status' => TicketStatusEnum::CLOSED->value]);

    $response = $this->actingAs($this->user)
        ->postJson($this->endpoint, ['message' => 'Reply to closed']);

    expect($response->assertBadRequest())
        ->and($response->json('status'))->toBe('error');
});
