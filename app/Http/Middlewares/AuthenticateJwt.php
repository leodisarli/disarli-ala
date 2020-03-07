<?php

namespace App\Http\Middlewares;

use Closure;
use JwtManager\JwtManager;

class AuthenticateJwt
{
    /**
     * handle an incoming request,
     *   authenticating with jwt token
     * @param Request $request
     * @param Closure $next
     * @throws \Exception
     * @return void
     */
    public function handle(
        $request,
        Closure $next
    ) {
        $token = $request->headers->get('Authorization') ?? null;
        $context = $request->headers->get('Context') ?? null;
        if (empty($token) || empty($context)) {
            throw new \Exception('Missing authorization', 401);
        }

        $jwt = $this->newJwtToken(
            $context
        );
        $jwt->isValid($token);
        $jwt->isOnTime($token);

        $data = $jwt->decodePayload($token);
        $audience = $data['aud'];
        $subject = $data['sub'];

        $iat = $data['iat'];
        $exp = $data['exp'];
        $validUntil = date('Y-m-d H:i:s', $iat + $exp);

        $needRefresh = $jwt->tokenNeedToRefresh($token);
        if ($needRefresh) {
            $token = $jwt->generate($audience, $subject);
            $validUntil = date('Y-m-d H:i:s', time() + $exp);
        }

        $request->jwtToken = [
            'token' => $token,
            'valid_until' => $validUntil,
        ];

        $request->info = config('version.info');

        return $next($request);
    }

    /**
     * @codeCoverageIgnore
     * create and return a new jwt manager
     * @param string $context
     * @return object
     */
    public function newJwtToken(
        string $context
    ) {
        return new JwtManager(
            config('app.jwt_app_secret'),
            $context
        );
    }
}
