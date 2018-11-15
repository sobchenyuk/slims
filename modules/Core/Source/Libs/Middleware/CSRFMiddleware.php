<?php
namespace Modules\Core\Source\Libs\Middleware;

use App\Helpers\SessionManager as Session;

class CSRFMiddleware
{
    public function __invoke($request, $response, $next)
    {
        if( Session::has('auth') && Session::get('auth') && $request->getMethod() == 'PUT' )
            return $next($request, $response);

        $guard = new \Slim\Csrf\Guard('csrf', $t, null, 10);
        return $guard($request, $response, $next);
    }
}