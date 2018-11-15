<?php

namespace Modules\Core\Source\MicroModules;

use Slim\Csrf\Guard;
use App\Source\BaseModule;
use Modules\Core\Source\Libs\Middleware\CSRFMiddleware;


class CSRFModule extends BaseModule
{
    const MODULE_NAME = 'csrf';
    protected static $loaded = false;

    public function registerDi()
    {
    	$this->container['csrf'] = function ($c) {
		    return new Guard;
		};
    }

    public function registerMiddleware()
    {
    	$this->app->add(new CSRFMiddleware());
    }
}
