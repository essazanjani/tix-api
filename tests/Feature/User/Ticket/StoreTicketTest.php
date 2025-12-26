<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use App\Enums\MediaCollectionEnum;
use App\Models\Ticket;

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->endpoint = route('ticket.store');
});

test('user can store a ticket with attachments', function () {
    $response = $this->postJson($this->endpoint, [
       'subject' => 'Test',
       'message' => 'Test message',
       'attachments' => [
           UploadedFile::fake()->image('attachment.jpg'),
           UploadedFile::fake()->image('attachment2.jpg')
       ]
    ]);

    $ticket = Ticket::query()->first();

    expect($response->assertCreated())
        ->and($response->json('status'))->toBe('success')
        ->and($this->user->tickets)->toHaveCount(1)
        ->and($ticket->getMedia(MediaCollectionEnum::TICKET->value))->toHaveCount(2);
});


test('fails when try to attach more than two attachments', function () {
    $response = $this->postJson($this->endpoint, [
        'subject' => 'Test',
        'message' => 'Test message',
        'attachments' => [
            UploadedFile::fake()->image('attachment.jpg'),
            UploadedFile::fake()->image('attachment2.jpg'),
            UploadedFile::fake()->image('attachment3.jpg')
        ]
    ]);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error')
        ->and($this->assertDatabaseEmpty('tickets'));
});
