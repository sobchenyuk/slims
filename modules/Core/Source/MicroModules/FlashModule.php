<?php

namespace Modules\Core\Source\MicroModules;

use Slim\Flash\Messages;
use App\Source\BaseModule;

class FlashModule extends BaseModule
{
    const MODULE_NAME = 'session_flash';
    protected static $loaded = false;

    public function registerDi()
    {
    	$this->container['flash'] = function () {
            return new Messages();
        };

        $flash = $this->container->flash;

        $this->container['flashMess'] = function () use ($flash) {
            return $flash->getMessages();
        };
    }
    
}
