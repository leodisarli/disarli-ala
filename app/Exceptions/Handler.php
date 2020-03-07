<?php

namespace App\Exceptions;

use App\Exceptions\Custom\ValidationException;
use Exception;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use ResponseJson\ResponseJson;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    private $response;

    public function __construct(
        ResponseJson $response
    ) {
        $this->response = $response;
    }

    public function render(
        $request,
        Exception $exception
    ) {
        $requestId = $request->requestId ?? '';
        $startProfile = $request->startProfile ?? 0;
        if ($exception instanceof ValidationException) {
            $result = $this->response->response(
                $requestId,
                $startProfile,
                $request->jwtToken,
                $exception->getMessages(),
                'A validation error occurrs'
            );
            return response()->json($result, 422);
        }

        if ($exception instanceof HttpException) {
            $result = $this->response->response(
                $requestId,
                $startProfile,
                $request->jwtToken,
                [],
                'Route not found'
            );
            return response()->json($result, 404);
        }

        $code = ($exception->getCode()) ? : 500;
        if (!is_int($code) || $code > 505 || $code < 0) {
            $code = 500;
        }
        $message = $exception->getMessage();
        if ($code == 500) {
            if (ENV('APP_ENV') == 'debug') {
                dd($exception);
            }
            $message = 'An unexpected error occurred, please try again later';
        }

        $result = $this->response->response(
            $requestId,
            $startProfile,
            $request->jwtToken,
            [],
            $message
        );
        return response()->json($result, $code);
    }
}
