<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public static function sendResponse($result, $message, $statusCode = 200)
    {   
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, $statusCode);
    }

    public static function sendError($error, $errorData = [], $statusCode = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorData)) {
            $response['data'] = $errorData;
        }

        return response()->json($response, $statusCode);
    }
}