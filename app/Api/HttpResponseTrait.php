<?php

namespace App\Api;

trait HttpResponseTrait
{
    public function errorResponse($errorCode = 500, $errorMessage){
        return response()->json([
            'statusCode' => $errorCode,
            'message' => $errorMessage,
        ], $errorCode);
    }

    public function successResponse($code = 200, $message, $data = null){
        return response()->json([
            'statusCode' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
