<?php

use App\Models\User;
use App\Models\PasswordResetCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->passwordResetCode = PasswordResetCode::factory()->for($this->user)->create(['code' => '12345']);
    $this->endpoint = route('verify');
});


test('verifies code successfully', function () {
    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'code' => $this->passwordResetCode->code
    ]);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($this->passwordResetCode->refresh()->verified_at)->not->toBeNull();
});


test('fails with incorrect code', function () {
    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'code' => '11111'
    ]);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error');
});


test('fails with expired code', function () {
    $this->passwordResetCode->update(['expires_at' => now()->subMinute()]);

    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'code' => $this->passwordResetCode->code
    ]);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error');
});
