<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseGenerator
{
    /**
     * Generate a success JSON response.
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public static function success(string $message, $data = null): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    /**
     * Generate a 201 Created JSON response.
     * @param $data
     * @param $message
     * @return JsonResponse
     */
    public static function created($data = null, $message = null): JsonResponse
    {
        return self::success($data, $message);
    }

    /**
     * Generate an error JSON response.
     * @param string $message
     * @return JsonResponse
     */
    public static function error(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Generate a 401 Unauthorized JSON response.
     * @param string|null $message
     * @param array|null $errors
     * @return JsonResponse
     */
    public static function unauthorized(?string $message = 'Unauthorized', array $errors = null): JsonResponse
    {
        return self::error($message, $errors, 401);
    }

    /**
     * Generate a 403 Forbidden JSON response.
     * @param string $message
     * @param $errors
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Forbidden', $errors = null): JsonResponse
    {
        return self::error($message, $errors, 403);
    }

    /**
     * Generate a 404 Not Found JSON response.
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Generate a 503 Service Unavailable JSON response.
     * @param string $message
     * @param $errors
     * @return JsonResponse
     */
    public static function serviceUnavailable(string $message = 'Service Unavailable', $errors = null): JsonResponse
    {
        return self::error($message, $errors, 503);
    }

}
