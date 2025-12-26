<?php

use App\Models\User;

beforeEach(function () {
    $this->password = 'Password123!';
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => $this->password
    ]);
    $this->endpoint = route('login');
});


test('user can login with correct credentials', function () {
    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'password' => $this->password
    ]);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($response->json('data.token'))->toBeString();
});


test('fails with wrong email', function () {
    $response = $this->postJson($this->endpoint, [
        'email' => 'wrong@email.com',
        'password' => $this->password
    ]);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error');
});


test('fails with wrong password', function () {
    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'password' => 'WrongPassword123!'
    ]);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error');
});


test('token expires in one day', function () {
    $this->travelTo(now());

    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'password' => $this->password,
        'remember_me' => false
    ]);

    $token = $this->user->tokens()->first();

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($token->expires_at->toDateTimeString())->toBe(now()->addDay()->toDateTimeString());
});


test('token expires in one week', function () {
    $this->travelTo(now());

    $response = $this->postJson($this->endpoint, [
        'email' => $this->user->email,
        'password' => $this->password,
        'remember_me' => true
    ]);

    $token = $this->user->tokens()->first();

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($token->expires_at->toDateTimeString())->toBe(now()->addWeek()->toDateTimeString());
});
