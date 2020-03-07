<?php

namespace Tests\Feature\Domains\Health;

use Tests\Feature\TestCaseFeature;

class HealthDbControllerTest extends TestCaseFeature
{
    /**
     * @covers \App\Domains\Health\Http\Controllers\HealthDbController::__construct
     * @covers \App\Domains\Health\Http\Controllers\HealthDbController::process
     */
    public function testHealthDb()
    {
        $this->json('GET', '/health/db', []);

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals(json_encode(['status' => 'online']), $this->response->getContent());
    }
}
