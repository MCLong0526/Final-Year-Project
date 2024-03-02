<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

trait StandardizeJsonResponse
{
    /**
     * Return a success JSON response.
     */
    protected function success(mixed $data = [], mixed $meta = null, ?string $message = null, int $code = 200): JsonResponse
    {
        // to fix potential bug
        // the bug is somehow it might return key pair value instead of array object (sometimes happen)
        if ($data instanceof Collection) {
            $data = $data->values();
        }

        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    protected function error(mixed $data = [], mixed $meta = null, ?string $message = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $code);
    }
}
