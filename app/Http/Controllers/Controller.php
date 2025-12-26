<?php

namespace App\Http\Controllers;

use App\Traits\ExceptionHandlerTrait;
use App\Traits\JsonResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use JsonResponseTrait, ExceptionHandlerTrait, AuthorizesRequests;
}
