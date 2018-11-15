<?php
namespace Modules\Core\Source\Libs\Middleware;

class CheckAjaxMiddleware
{

    public function __invoke($request, $response, $next)
    {
        // create a new property in the container to hold the route name
        // for later use in ANY controller constructor being 
        // instantiated by the router
        if( $request->isXhr() )
        	return $next($request, $response);
        else
        	return $response->withStatus(302)->withHeader('Location','/');
    }
}