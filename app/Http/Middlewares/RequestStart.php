<?php

namespace App\Http\Middlewares;

use Uuid\Uuid;

use Closure;

class RequestStart
{
    private $uuid;

    /**
     * contructor
     * @param Uuid $uuid
     * @return void
     */
    public function __construct(
        Uuid $uuid
    ) {
        $this->uuid = $uuid;
    }

    /**
     * handle an incoming request,
     *   setting start and request id
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(
        $request,
        Closure $next
    ) {
        $request->requestId = $this->uuid->uuid4();
        $request->startProfile = microtime(true);
        
        return $next($request);
    }
}
