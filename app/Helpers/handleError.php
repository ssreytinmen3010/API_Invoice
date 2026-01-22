<?php

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

if (!function_exists('error_response')) {
    /**
     * Return a standardized JSON error response.
     *
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    function error_response(Throwable $exception)
    {
        $statusCode = 500;
        $message = 'Something went wrong';
        $errors = [];

        if ($exception instanceof ValidationException) {
            $statusCode = 422;
            $message = 'Validation failed';
            $errors = $exception->errors();
        } elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'HTTP error';
        } else {
            $message = $exception->getMessage() ?: $message;
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
