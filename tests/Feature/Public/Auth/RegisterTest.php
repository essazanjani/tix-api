<?php

use App\Models\User;

beforeEach(function () {
    $this->endpoint = route('register');
});

test('user registered successfully', function () {
    $response = $this->post($this->endpoint, [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!'
    ]);

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($this->assertDatabaseHas('users', ['email' => 'test@example.com']));
});


test('fails with repeated email', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);

    $response = $this->postJson($this->endpoint, [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => $user->email,
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!'
    ]);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error')
        ->and($this->assertDatabaseCount('users', 1));
});


test('fails with different password', function () {
    $response = $this->postJson($this->endpoint, [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'WrongPassword123!'
    ]);

    expect($response->assertUnprocessable())
        ->and($response->json('status'))->toBe('error')
        ->and($this->assertDatabaseEmpty('users'));
});
