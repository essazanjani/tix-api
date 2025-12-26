<?php

namespace App\Traits;

use App\Exceptions\CustomException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Illuminate\View\ViewException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;
use TypeError;

trait ExceptionHandlerTrait
{
    use JsonResponseTrait;

    public function reportException(Throwable $handler): Response
    {
        return match (true) {
            $handler instanceof ValidationException
            => $this->validationError($handler),

            $handler instanceof CustomException
            => $this->reportError($handler, $handler->getMessage(), (int)$handler->getCode()),

            $handler instanceof TokenMismatchException
            => $this->reportError($handler, trans('base.exceptions.token_mismatch'), Response::HTTP_BAD_REQUEST),

            $handler instanceof QueryException
            => $this->reportError($handler, trans('base.exceptions.query'), Response::HTTP_BAD_REQUEST),

            $handler instanceof InternalErrorException,
                $handler instanceof FatalError
            => $this->reportError($handler, trans('base.exceptions.internal_server'), Response::HTTP_INTERNAL_SERVER_ERROR),

            $handler instanceof ModelNotFoundException
            => $this->reportError($handler, trans('base.exceptions.model_not_found'), Response::HTTP_NOT_FOUND),

            $handler instanceof AuthorizationException,
                $handler instanceof AccessDeniedHttpException
            => $this->reportError($handler, trans('base.exceptions.authorization'), Response::HTTP_FORBIDDEN),

            $handler instanceof AuthenticationException
            => $this->reportError($handler, trans('base.exceptions.authentication'), Response::HTTP_UNAUTHORIZED),

            $handler instanceof MethodNotAllowedHttpException
            => $this->reportError($handler, trans('base.exceptions.method_not_allowed_http'), Response::HTTP_METHOD_NOT_ALLOWED),

            $handler instanceof NotFoundHttpException,
                $handler instanceof RouteNotFoundException
            => $this->reportError($handler, trans('base.exceptions.not_found_http'), Response::HTTP_NOT_FOUND),

            $handler instanceof ViewException
            => $this->reportError($handler, trans('base.exceptions.view'), Response::HTTP_BAD_REQUEST),

            $handler instanceof TypeError
            => $this->reportError($handler, trans('base.exceptions.type_error'), Response::HTTP_INTERNAL_SERVER_ERROR),

            $handler instanceof ThrottleRequestsException
            => $this->reportError($handler, trans('base.exceptions.throttle_request'), Response::HTTP_TOO_MANY_REQUESTS),

            $handler instanceof BackedEnumCaseNotFoundException
            => $this->reportError($handler, trans('base.exceptions.backed_enum_case_not_found'), Response::HTTP_INTERNAL_SERVER_ERROR),

            default
            => $this->reportError($handler, $handler->getMessage(), Response::HTTP_BAD_REQUEST)
        };
    }


    public function isJsonApi(Request $request): bool
    {
        return $request->is('api/*') ||
            $request->is('oauth/*')  ||
            $request->expectsJson();
    }


    private function reportError(Throwable $handler, string|array $message, int $statusCode): Response
    {
        if ($this->shouldNotify($handler)) {
            $this->sendNotify($handler, $statusCode);
        }

        return $this->errorResponse(
            $this->resolveMessage($handler, $message),
            $statusCode
        );
    }


    private function shouldNotify(Throwable $handler): bool
    {
        return !(
            $handler instanceof ValidationException     ||
            $handler instanceof AuthenticationException ||
            $handler instanceof NotFoundHttpException   ||
            $handler instanceof ModelNotFoundException  ||
            $handler instanceof BadRequestException
        );
    }


    private function sendNotify(Throwable $handler, int $statusCode): void
    {
        //
    }


    private function resolveMessage(Throwable $handler, string|array $defaultMessage): string|array
    {
        return (!App::isProduction() && filled($handler->getMessage()) && !$handler instanceof ValidationException)
            ? $handler->getMessage()
            : $defaultMessage;
    }


    private function validationError(ValidationException $exception): Response
    {
        $errors = $exception->validator->errors()->getMessages();

        $message = $errors
            ? array_map(fn($msgs) => $msgs[0], $errors)
            : $exception->getMessage();

        return $this->reportError($exception, $message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
