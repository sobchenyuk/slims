<?php

namespace Modules\Core\Source\Libs\Middleware;

use App\Helpers\SessionManager as Session;

class AuthMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        global $container;
        
        if(!Session::has('auth') || !Session::get('auth') ){
            $container->flash->addMessage('errors', 'Please authorize');
    	    return $response->withStatus(302)->withHeader('Location','/auth/login');
        }

        $response = $next($request, $response);
    	return $response;
    }
}