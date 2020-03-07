<?php

namespace App\Http\Middlewares;

use Laravel\Lumen\Routing\Router;
use Mockery;
use PHPUnit\Framework\TestCase;

class BaseRequestTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\BaseRequest::getRoutes
     */
    public function testGetRoutes()
    {
        $response = [
            'uses' => 'UserListController@process',
            'validator' => 'App\Domains\User\Http\Validators\UserListValidator',
            'parameters' => 'App\Domains\User\Http\Parameters\UserParameters',
        ];

        $routerMock = Mockery::mock(Router::class)
            ->shouldReceive('getRoutes')
            ->withNoArgs()
            ->andReturn([
                'get/user/list' => [
                    'action' => $response,
                ],
            ])
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('method')
            ->withNoArgs()
            ->andReturn('get')
            ->shouldReceive('getPathInfo')
            ->withNoArgs()
            ->andReturn('/user/list')
            ->getMock();

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $baseRequest->shouldReceive('newApp')
            ->withNoArgs()
            ->andReturn((object) ['router' => $routerMock]);

        $middleware = $baseRequest->getRoutes();

        $this->assertEquals($response, $middleware['get/user/list']['action']);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::getRouteDetails
     */
    public function testGetRouteDetails()
    {
        $response = [
            'uses' => 'UserListController@process',
            'validator' => 'App\Domains\User\Http\Validators\UserListValidator',
            'parameters' => 'App\Domains\User\Http\Parameters\UserParameters',
        ];

        $routerMock = Mockery::mock(Router::class)
            ->shouldReceive('getRoutes')
            ->withNoArgs()
            ->andReturn([
                'get/user/list' => [
                    'action' => $response,
                ],
            ])
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('method')
            ->withNoArgs()
            ->andReturn('get')
            ->shouldReceive('getPathInfo')
            ->withNoArgs()
            ->andReturn('/user/list')
            ->getMock();

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $baseRequest->shouldReceive('newApp')
            ->withNoArgs()
            ->andReturn((object) ['router' => $routerMock]);

        $middleware = $baseRequest->getRouteDetails($requestMock);

        $this->assertEquals($response, $middleware);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::getRouteDetails
     */
    public function testGetAndNotHasRouteDetails()
    {
        $routerMock = Mockery::mock(Router::class)
            ->shouldReceive('getRoutes')
            ->withNoArgs()
            ->andReturn([
                'get/user/list' => [
                    'action' => null,
                ],
            ])
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('method')
            ->withNoArgs()
            ->andReturn('get')
            ->shouldReceive('getPathInfo')
            ->withNoArgs()
            ->andReturn('/user/list')
            ->getMock();

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $baseRequest->shouldReceive('newApp')
            ->withNoArgs()
            ->andReturn((object) ['router' => $routerMock]);

        $middleware = $baseRequest->getRouteDetails($requestMock);

        $this->assertNull($middleware);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::fixKey
     */
    public function testFixKeyValid()
    {
        $goodKey = '/tag/dead_detail';

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $key = $baseRequest->fixKey($goodKey);

        $this->assertEquals($key, $goodKey);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::fixKey
     */
    public function testFixKeyInvalid()
    {
        $badKey = '/tag/dead_detail/{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}';
        $goodKey = '/tag/dead_detail';

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $key = $baseRequest->fixKey($badKey);

        $this->assertEquals($key, $goodKey);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::fixPath
     */
    public function testFixPath()
    {
        $goodPath = '/tag/dead_detail';

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $path = $baseRequest->fixPath($goodPath);

        $this->assertEquals($path, $goodPath);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::fixPath
     */
    public function testFixPathInvalid()
    {
        $badPath = '/tag/dead_detail/85665c11-be30-4f26-92b2-6f0e350574e4';
        $goodPath = '/tag/dead_detail';

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $path = $baseRequest->fixPath($badPath);

        $this->assertEquals($path, $goodPath);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
