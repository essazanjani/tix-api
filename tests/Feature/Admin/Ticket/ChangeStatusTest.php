<?php

use App\Models\Ticket;
use App\Models\User;
use App\Enums\TicketStatusEnum;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo('TicketController@changeStatus');
    $this->actingAs($this->admin);
    $this->user = User::factory()->create();
    $this->ticket = Ticket::factory()->for($this->user)->create();
    $this->endpoint = route('admin.ticket.changeStatus', $this->ticket);
});


test('admin can change ticket status to closed', function () {
    $response = $this->patchJson($this->endpoint, [
        'status' => TicketStatusEnum::CLOSED->value
    ]);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($this->ticket->fresh()->status)->toBe(TicketStatusEnum::CLOSED->value);
});


test('admin can change ticket status to pending', function () {
    $response = $this->patchJson($this->endpoint, [
        'status' => TicketStatusEnum::PENDING->value
    ]);

    expect($response->assertOk())
        ->and($this->ticket->fresh()->status)->toBe(TicketStatusEnum::PENDING->value);
});


test('admin can change ticket status to open', function () {
    $this->ticket->update(['status' => TicketStatusEnum::CLOSED->value]);

    $response = $this->patchJson($this->endpoint, [
        'status' => TicketStatusEnum::OPEN->value
    ]);

    expect($response->assertOk())
        ->and($this->ticket->fresh()->status)->toBe(TicketStatusEnum::OPEN->value);
});


test('fails if ticket is not parent', function () {
    $reply = Ticket::factory()->reply($this->ticket)->create();

    $response = $this->patchJson(route('admin.ticket.changeStatus', $reply), [
        'status' => TicketStatusEnum::CLOSED->value
    ]);

    expect($response->assertBadRequest())
        ->and($response->json('status'))->toBe('error');
});


test('fails with invalid status', function () {
    $response = $this->patchJson($this->endpoint, [
        'status' => 999
    ]);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error');
});


test('fails without permission', function () {
    $admin = User::factory()->create();
    $this->actingAs($admin);

    $response = $this->patchJson($this->endpoint, [
        'status' => TicketStatusEnum::CLOSED->value
    ]);

    expect($response->assertForbidden());
});
