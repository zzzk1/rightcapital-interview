<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;

class ApiResult
{
    public static function success($message, $data, $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function fail($message, $status = 500): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => null,
        ], $status);
    }
}
