<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Log;


class BaseController
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        Log::channel('api')->info('API Success', [
            'message' => $message,
            'timestamp' => now(),
            'data' => $result
        ]);

        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 400)
    {
        Log::channel('api')->error('API Error', [
            'message' => $error,
            'details' => $errorMessages,
            'status_code' => $code,
            'timestamp' => now(),
        ]);

        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }





}