<?php

namespace App\Http\Middlewares;

use App\Domains\User\Http\Parameters\UserParameters;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestParametersTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\RequestParameters::handle
     */
    public function testHandle()
    {
        $routeDetails = [
            'uses' => 'UserListController@process',
            'validator' => 'App\Domains\User\Http\Validators\UserListValidator',
            'parameters' => 'App\Domains\User\Http\Parameters\UserParameters',
        ];

        $construct = [
            'fields' => 'fields',
            'order' => 'order',
            'class' => 'class',
        ];

        $userParametersSpy = Mockery::mock(UserParameters::class);

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('get')
            ->with('fields')
            ->andReturn('fields')
            ->shouldReceive('get')
            ->with('order')
            ->andReturn('order')
            ->shouldReceive('get')
            ->with('class')
            ->andReturn('class')
            ->getMock();

        $requestParameters = Mockery::mock(RequestParameters::class)->makePartial();
        $requestParameters->shouldReceive('getRouteDetails')
            ->with($requestMock)
            ->andReturn($routeDetails)
            ->shouldReceive('newClass')
            ->once()
            ->with($routeDetails['parameters'], $construct)
            ->andReturn($userParametersSpy);

        $middleware = $requestParameters->handle($requestMock, function ($request) {
            $this->assertInstanceOf(UserParameters::class, $request->params);
        });
    }

    /**
     * @covers \App\Http\Middlewares\RequestParameters::handle
     */
    public function testHandleAndNoParams()
    {
        $routeDetails = [
            'uses' => 'UserListController@process',
            'validator' => 'App\Domains\User\Http\Validators\UserListValidator',
        ];

        $construct = [
            'fields' => 'fields',
            'order' => 'order',
            'class' => 'class',
        ];

        $userParametersSpy = Mockery::mock(UserParameters::class);

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('get')
            ->with('fields')
            ->never()
            ->andReturn('fields')
            ->shouldReceive('get')
            ->with('order')
            ->never()
            ->andReturn('order')
            ->shouldReceive('get')
            ->with('class')
            ->never()
            ->andReturn('class')
            ->getMock();

        $requestParameters = Mockery::mock(RequestParameters::class)->makePartial();
        $requestParameters->shouldReceive('getRouteDetails')
            ->with($requestMock)
            ->andReturn($routeDetails)
            ->shouldReceive('newClass')
            ->never()
            ->andReturn($userParametersSpy);

        $middleware = $requestParameters->handle($requestMock, function ($request) {
            $this->assertNull($request->params);
        });
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
