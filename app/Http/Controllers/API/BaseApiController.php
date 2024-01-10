<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller as Controller;


class BaseApiController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message, $code = 200): JsonResponse
    {
        $response = [
            'message' => $message,
            'data' => $result,
            'errors' => null,
        ];


        return response()->json($response, $code);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $code = 400, $errorMessages = []): JsonResponse
    {
        $response = [
            'message' => $error,
            'data' => null,
            'errors' => null,
        ];


        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}