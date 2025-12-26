<?php

use App\Models\User;
use App\Models\PasswordResetCode;
use App\Events\ForgotPassword;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Event::fake();
    $this->user = User::factory()->create();
    $this->endpoint = route('forgot-password');
});


test('creates reset code and dispatches event', function () {
    $response = $this->postJson($this->endpoint, ['email' => $this->user->email]);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($this->assertDatabaseHas('password_reset_codes', ['user_id' => $this->user->id]));

    Event::assertDispatched(ForgotPassword::class);
});


test('fails if user has active reset code', function () {
    PasswordResetCode::factory()->for($this->user)->create();

    $response = $this->postJson($this->endpoint, ['email' => $this->user->email]);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error');
});
