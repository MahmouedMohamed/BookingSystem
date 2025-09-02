<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Send any success response
     *
     * @param  string  $message
     * @param  array|object  $data
     * @param  string  $key
     * @param  int  $statusCode
     */
    public function sendSuccessResponse($message = 'success', $data = [], $key = 'items', $statusCode = 200)
    {
        return response()->json([
            'code' => $statusCode,
            'success' => true,
            'message' => $message,
            $key => $data,
        ], $statusCode);
    }

    /**
     * Send any error response
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @param  string  $customErrorCode
     */
    public function sendErrorResponse($message, $statusCode = 500, $customErrorCode = 0)
    {
        return response()->json([
            'code' => $statusCode,
            'success' => false,
            'message' => $message,
            'item' => [],
            'error_code' => $customErrorCode,
        ], $statusCode);
    }

    /**
     * Send any validation errors response
     *
     * @param  array  $errors
     */
    public function sendValidationResponse($errors, $message = 'validation error')
    {
        return response()->json([
            'code' => 422,
            'success' => false,
            'message' => $message,
            'item' => [],
            'errors' => $errors,
        ], 422);
    }

    /**
     * Send unauthorized error response
     */
    public function sendUnauthorizedResponse()
    {
        return response()->json([
            'code' => 401,
            'success' => false,
            'message' => __('app.unauthorized'),
            'item' => [],
        ], 401);
    }

    public function sendPermissionErrorResponse()
    {
        return response()->json([
            'code' => 403,
            'success' => false,
            'message' => __('app.insufficient_permissions'),
            'item' => [],
        ], 403);
    }
}
