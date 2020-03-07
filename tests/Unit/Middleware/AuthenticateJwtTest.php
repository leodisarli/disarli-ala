<?php

namespace App\Http\Middlewares;

use JwtManager\JwtManager;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;

class AuthenticateJwtTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\AuthenticateJwt::handle
     */
    public function testHandleAndMissingAuthorization()
    {
        $headerBagMock = Mockery::mock(HeaderBag::class)
            ->shouldReceive('get')
            ->with('Authorization')
            ->andReturn(null)
            ->shouldReceive('get')
            ->with('Context')
            ->andReturn(null)
            ->getMock();

        $requestMock = (object) [
            'headers' => $headerBagMock,
        ];

        $middleware = new AuthenticateJwt;

        $this->expectExceptionObject(new \Exception('Missing authorization', 401));

        $response = $middleware->handle($requestMock, function () {
        });
    }

    /**
     * @covers \App\Http\Middlewares\AuthenticateJwt::handle
     */
    public function testHandleAndDontNeedRefresh()
    {
        $jwt = [
            'aud' => 'teste',
            'sub' => 'teste2',
            'iat' => time(date('Y-m-d H:i:s')),
            'exp' => 900,
        ];

        $jwtManager = Mockery::mock(JwtManager::class)
            ->shouldReceive('isValid')
            ->with('123456')
            ->andReturn(true)
            ->shouldReceive('isOnTime')
            ->with('123456')
            ->andReturn(true)
            ->shouldReceive('decodePayload')
            ->with('123456')
            ->andReturn($jwt)
            ->shouldReceive('tokenNeedToRefresh')
            ->with('123456')
            ->andReturn(false)
            ->shouldReceive('generate')
            ->never()
            ->with($jwt['aud'], $jwt['sub'])
            ->andReturn('1234567')
            ->getMock();

        $headerBagMock = Mockery::mock(HeaderBag::class)
            ->shouldReceive('get')
            ->with('Authorization')
            ->andReturn('123456')
            ->shouldReceive('get')
            ->with('Context')
            ->andReturn('test')
            ->getMock();

        $requestMock = (object) [
            'headers' => $headerBagMock,
        ];

        $middleware = Mockery::mock(AuthenticateJwt::class)->makePartial();
        $middleware->shouldReceive('newJwtToken')
            ->with('test')
            ->andReturn($jwtManager);

        $middleware->handle($requestMock, function ($request) use ($jwt) {
            $this->assertEquals($request->jwtToken, [
                'token' =>'123456',
                'valid_until' => date('Y-m-d H:i:s', $jwt['iat'] + $jwt['exp']),
            ]);
        });
    }

    /**
     * @covers \App\Http\Middlewares\AuthenticateJwt::handle
     */
    public function testHandleAndNeedToRefresh()
    {
        $jwt = [
            'aud' => 'teste',
            'sub' => 'teste2',
            'iat' => time(date('Y-m-d H:i:s')),
            'exp' => 900,
        ];

        $jwtManager = Mockery::mock(JwtManager::class)
            ->shouldReceive('isValid')
            ->with('123456')
            ->andReturn(true)
            ->shouldReceive('isOnTime')
            ->with('123456')
            ->andReturn(true)
            ->shouldReceive('decodePayload')
            ->with('123456')
            ->andReturn($jwt)
            ->shouldReceive('tokenNeedToRefresh')
            ->with('123456')
            ->andReturn(true)
            ->shouldReceive('generate')
            ->once()
            ->with($jwt['aud'], $jwt['sub'])
            ->andReturn('1234567')
            ->getMock();

        $headerBagMock = Mockery::mock(HeaderBag::class)
            ->shouldReceive('get')
            ->with('Authorization')
            ->andReturn('123456')
            ->shouldReceive('get')
            ->with('Context')
            ->andReturn('test')
            ->getMock();

        $requestMock = (object) [
            'headers' => $headerBagMock,
        ];

        $middleware = Mockery::mock(AuthenticateJwt::class)->makePartial();
        $middleware->shouldReceive('newJwtToken')
            ->with('test')
            ->andReturn($jwtManager);

        $middleware->handle($requestMock, function ($request) use ($jwt) {
            $this->assertEquals($request->jwtToken['token'], '1234567');
        });
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
