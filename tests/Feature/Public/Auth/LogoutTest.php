<?php

use App\Models\User;

test('user can logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)->postJson(route('logout'));

    expect($response->assertOk())
        ->and($response->json('status'))->toBe('success')
        ->and($user->tokens()->count())->toBe(0);
});
