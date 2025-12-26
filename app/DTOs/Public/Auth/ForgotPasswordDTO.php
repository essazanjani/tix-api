<?php

namespace App\DTOs\Public\Auth;

class ForgotPasswordDTO
{
    public function __construct(public readonly string $email)
    {}


    public static function fromRequest(array $data): static
    {
        return new static($data['email']);
    }
}
