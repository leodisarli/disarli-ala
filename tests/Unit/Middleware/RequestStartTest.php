<?php

namespace App\Http\Middlewares;

use Uuid\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestStartTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\RequestStart::__construct
     */
    public function testCreateMiddleware()
    {
        $uuidSpy = Mockery::spy(Uuid::class);
        $requestStart = new RequestStart($uuidSpy);
        $this->assertInstanceOf(RequestStart::class, $requestStart);
    }

    /**
     * @covers \App\Http\Middlewares\RequestStart::handle
     */
    public function testHandle()
    {
        $requestSpy = Mockery::spy(Request::class);

        $uuidMock = Mockery::mock(Uuid::class)
            ->shouldReceive('uuid4')
            ->withNoArgs()
            ->andReturn('hashuuid')
            ->getMock();

        $requestStart = new RequestStart($uuidMock);
        $handle = $requestStart->handle($requestSpy, function ($request) {
            $this->assertEquals('hashuuid', $request->requestId);
            $this->assertNotNull($request->startProfile);
        });
    }
    
    public function tearDown()
    {
        Mockery::close();
    }
}
