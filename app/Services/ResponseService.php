<?php
namespace App\Services;

class ResponseService
{
    public function success($data = [], $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function failure($message = 'Failure', $statusCode = 400)
    {
        return response()->json([
            'status' => 'failure',
            'message' => $message,
        ], $statusCode);
    }

    public function notFound($message = 'Not Found', $statusCode = 404)
    {
        return response()->json([
            'status' => 'not_found',
            'message' => $message,
        ], $statusCode);
    }
}
