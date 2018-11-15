<?php
namespace App\Source\Events;

use Slim\App;
use Symfony\Component\EventDispatcher\Event;

class BaseAppEvent extends Event
{

    /**
     * @var Slim\App
     */
    protected $app;

    /**
     * @var Slim\Container
     */
    protected $container;

    protected $undefinedObject;

    public function __construct(App $app = null, $param = null)
    {
        $this->app = $app;
        $this->container = $app->getContainer();
        $this->undefinedObject = $param;
    }

    /**
     * @return \Slim\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Slim\App
     */
    public function getApp()
    {
        return $this->app;
    }

    public function getLogger()
    {
        return $this->getContainer()->logger;
    }

    public function getParams()
    {
        return $this->undefinedObject;
    }

}
