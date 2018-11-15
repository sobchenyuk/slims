<?php
namespace App\Middleware;

use Slim\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class ABaseMiddleware
{
    protected $c; // container

    public function __construct(Container $c)
    {
        $this->c = $c; // store the instance as a property
    }

    abstract public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next); 
}
