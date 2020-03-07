<?php

namespace Tests\Feature;

use Laravel\Lumen\Testing\TestCase;

class TestCaseFeature extends TestCase
{
    protected $header;

    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $credencials = [
            'token' => '379ba1a99843b24ada4b4e068afa0a872f11392859424cab1b86764c6ee7cddf',
            'secret' => 'ba135548fce772e5f82f3f477a19b4479f2430c7d0933789ac427c2f97c079bf',
        ];

        $this->json('POST', '/auth/generate', $credencials);

        $token = json_decode($this->response->getContent(), true)['data']['token'];

        $this->header = [
            'Authorization' => $token,
            'Context' => 'develop',
        ];
    }
}
