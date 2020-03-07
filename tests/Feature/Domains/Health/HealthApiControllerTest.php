<?php

namespace Tests\Feature\Domains\Health;

use Tests\Feature\TestCaseFeature;

class HealthApiControllerTest extends TestCaseFeature
{
    /**
     * @covers \App\Domains\Health\Http\Controllers\HealthApiController::process
     */
    public function testHealthApi()
    {
        $this->json('GET', '/health/api', []);

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals(json_encode(['status' => 'online']), $this->response->getContent());
    }
}
