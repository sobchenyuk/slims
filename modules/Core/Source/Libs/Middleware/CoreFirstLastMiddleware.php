<?php
namespace Modules\Core\Source\Libs\Middleware;

use App\Source\Events\BaseMiddlewareEvent;

class CoreFirstLastMiddleware
{
    protected $c; // container

    public function __construct($c)
    {
        $this->c = $c; // store the instance as a property
    }

    public function core($request, $response, $next)
    {
        $event = new BaseMiddlewareEvent($this->c, $request, $response);
        $event = $this->c->dispatcher->dispatch('middleware.core.before', $event);
        $request  = $event->getRequest();
        $response = $event->getResponse();

        $response = $next($request, $response);
        
        $event = new BaseMiddlewareEvent($this->c, $request, $response);
        $event = $this->c->dispatcher->dispatch('middleware.core.after', $event);
        $response = $event->getResponse();

        return $response;
    }
}