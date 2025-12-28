<?php

use App\Models\Ticket;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo('TicketController@index');
    $this->actingAs($this->admin);
    $this->endpoint = route('admin.ticket.index');
});


test('returns paginated parent tickets only', function () {
    $user = User::factory()->create();
    $parentTicket = Ticket::factory()->for($user)->create();
    Ticket::factory()->reply($parentTicket)->create();

    $response = $this->getJson($this->endpoint);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($response->json('data.data'))->toHaveCount(1)
        ->and($response->json('data.data.0.id'))->toBe($parentTicket->id)
        ->and($response->json('data.data.0.user_full_name'))->toBe($user->full_name);
});


test('returns unseen replies count from users', function () {
    $user = User::factory()->create();
    $parentTicket = Ticket::factory()->for($user)->create();

    Ticket::factory()->reply($parentTicket)->create(['admin_id' => null, 'seen_at' => null]);
    Ticket::factory()->reply($parentTicket)->create(['admin_id' => null, 'seen_at' => null]);
    Ticket::factory()->reply($parentTicket)->create(['admin_id' => $this->admin->id, 'seen_at' => null]);

    $response = $this->getJson($this->endpoint);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($response->json('data.data.0.unseen_replies_count'))->toBe(2);
});


test('does not count already seen replies', function () {
    $user = User::factory()->create();
    $parentTicket = Ticket::factory()->for($user)->create();

    Ticket::factory()->reply($parentTicket)->create(['admin_id' => null, 'seen_at' => now()]);
    Ticket::factory()->reply($parentTicket)->create(['admin_id' => null, 'seen_at' => null]);

    $response = $this->getJson($this->endpoint);

    expect($response->assertOk())
        ->and($response->json('data.data.0.unseen_replies_count'))->toBe(1);
});


test('fails without permission', function () {
    $admin = User::factory()->create();
    $this->actingAs($admin);

    $response = $this->getJson($this->endpoint);

    expect($response->assertForbidden());
});
