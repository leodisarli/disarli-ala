<?php

namespace App\Http\Middlewares;

use App\Domains\User\Http\Validators\UserListValidator;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestValidatorTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\RequestValidator::handle
     */
    public function testHandle()
    {
        $routeDetails = [
            'uses' => 'UserListController@process',
            'validator' => 'App\Domains\User\Http\Validators\UserListValidator',
            'parameters' => 'App\Domains\User\Http\Parameters\UserParameters',
        ];

        $userListValidatorMock = Mockery::mock(UserListValidator::class)
            ->shouldReceive('validate')
            ->with([])
            ->once()
            ->andReturn(true)
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('all')
            ->withNoArgs()
            ->andReturn([])
            ->getMock();

        $requestValidator = Mockery::mock(RequestValidator::class)->makePartial();
        $requestValidator->shouldReceive('getRouteDetails')
            ->with($requestMock)
            ->andReturn($routeDetails)
            ->shouldReceive('newClass')
            ->once()
            ->with($routeDetails['validator'])
            ->andReturn($userListValidatorMock);

        $middleware = $requestValidator->handle($requestMock, function ($request) {
            $this->assertEquals(true, $request->validFields);
        });
    }

    /**
     * @covers \App\Http\Middlewares\RequestValidator::handle
     */
    public function testHandleAndNoValidator()
    {
        $routeDetails = [
            'uses' => 'UserListController@process',
            'parameters' => 'App\Domains\User\Http\Parameters\UserParameters',
        ];

        $userListValidatorMock = Mockery::mock(UserListValidator::class)
            ->shouldReceive('validate')
            ->with([])
            ->never()
            ->andReturn(true)
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('all')
            ->withNoArgs()
            ->never()
            ->andReturn([])
            ->getMock();

        $requestValidator = Mockery::mock(RequestValidator::class)->makePartial();
        $requestValidator->shouldReceive('getRouteDetails')
            ->with($requestMock)
            ->andReturn($routeDetails)
            ->shouldReceive('newClass')
            ->never()
            ->andReturn($userListValidatorMock);

        $middleware = $requestValidator->handle($requestMock, function ($request) {
            $this->assertNull($request->validFields ?? null);
        });
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
