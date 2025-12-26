<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait JsonResponseTrait
{
    protected function successResponse(mixed $data = null, ?string $message = null): JsonResponse
    {
        return response()->json([
            'status' => "success",
            'message' => $message ?? trans('base.messages.success'),
            'data' => $data
        ], Response::HTTP_OK);
    }


    protected function createdResponse(mixed $data = null, ?string $message = null): JsonResponse
    {
        return response()->json([
            'status' => "success",
            'message' => $message ?? trans('base.messages.created'),
            'data' => $data
        ], Response::HTTP_CREATED);
    }


    protected function updatedResponse(mixed $data = null, ?string $message = null): JsonResponse
    {
        return response()->json([
            'status' => "success",
            'message' => $message ?? trans('base.messages.updated'),
            'data' => $data
        ], Response::HTTP_ACCEPTED);
    }


    protected function deletedResponse(mixed $data = null, ?string $message = null): JsonResponse
    {
        return response()->json([
            'status' => "success",
            'message' => $message ?? trans('base.messages.deleted'),
            'data' => $data
        ], Response::HTTP_NO_CONTENT);
    }


    protected function errorResponse(string|array $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'status' => "error",
            'message' => $message
        ], $statusCode);
    }
}
