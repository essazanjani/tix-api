<?php

use App\Models\User;
use App\Models\PasswordResetCode;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->password = 'Password123!';
    $this->user = User::factory()->create();
    $this->passwordResetCode = PasswordResetCode::factory()->for($this->user)->create(['verified_at' => now()]);
    $this->endpoint = route('reset-password');
});


test('resets password successfully', function () {
    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'password' => $this->password,
        'password_confirmation' => $this->password
    ]);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and(Hash::check($this->password, $this->user->refresh()->password))->toBeTrue();
});


test('fails if code is not verified', function () {
    $this->passwordResetCode->update(['verified_at' => null]);

    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'password' => $this->password,
        'password_confirmation' => $this->password
    ]);

    expect($response->assertBadRequest())
        ->and($response->json('status'))->toBe('error');
});


test('fails if verified code is expired', function () {
    $this->passwordResetCode->update(['expires_at' => now()->subMinute()]);

    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'password' => $this->password,
        'password_confirmation' => $this->password
    ]);

    expect($response->assertBadRequest())
        ->and($response->json('status'))->toBe('error');
});
