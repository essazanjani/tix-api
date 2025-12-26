<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CustomException extends Exception
{
    public function __construct(?string $message = null, ?int $code = null)
    {
        parent::__construct(
            $message ?? trans('base.exceptions.bad_request'),
            $code ?? Response::HTTP_BAD_REQUEST
        );
    }
}
