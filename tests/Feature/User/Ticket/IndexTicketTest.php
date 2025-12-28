<?php

use App\Models\Ticket;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->endpoint = route('user.ticket.index');
});


test('returns last 10 user tickets', function () {
    Ticket::factory(11)->for($this->user)->create();

    $response = $this->getJson($this->endpoint);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($response->json('data.data'))->toHaveCount(10);
});


test('does not return non parent tickets', function () {
    $ticket = Ticket::factory()->for($this->user)->create();
    Ticket::factory()->reply($ticket);

    $response = $this->getJson($this->endpoint);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($response->json('data.data.parent_id'))->toBeNull();
});
