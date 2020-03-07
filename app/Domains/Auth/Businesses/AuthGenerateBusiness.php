<?php

namespace App\Domains\Auth\Businesses;

use JwtManager\JwtManager;

class AuthGenerateBusiness
{
    /**
     * process the request with business rules
     * @param array $data
     * @throws \Exception
     * @return array
     */
    public function process(
        array $data
    ) : array {
        $auth = $this->getFromToken(
            $data['token'],
            $data['secret']
        );

        if (empty($auth)) {
            throw new \Exception('Invalid credentials', 401);
        }

        return $this->generateToken(
            $auth['name'],
            $data['token']
        );
    }

    /**
     * generate token
     * @param string $audience
     * @param string $subject
     * @return array
     */
    public function generateToken(
        string $audience,
        string $subject
    ) : array {
        $jwt = $this->newJwtToken(
            $audience
        );
        $jwtToken = $jwt->generate($audience, $subject);
        $validate = date('Y-m-d H:i:s', time() + $jwt->getExpire());
        return [
            'token' => $jwtToken,
            'valid_until' => $validate,
        ];
    }

    /**
     * search on config for token and secret to authenticate
     * @param string $token
     * @param string $secret
     * @return array|null
     */
    public function getFromToken(
        string $token,
        string $secret
    ) :? array {
        $tokens = $this->getConfig('tokens.data');
        $tokens[$token]['secret'] ?? null;

        if ($tokens[$token]['secret'] == $secret) {
            return $tokens[$token];
        }
        return null;
    }

    /**
     * @codeCoverageIgnore
     * get laravel config
     * @param string $config
     * @return array|null
     */
    public function getConfig(
        string $config
    ) : ? array {
        return config($config);
    }

    /**
     * @codeCoverageIgnore
     * create and return jwt manager
     * @param string $context
     * @return JwtManager
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
