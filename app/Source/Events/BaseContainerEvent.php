<?php
namespace App\Source\Events;

use \Slim\Container;
use Symfony\Component\EventDispatcher\Event;

class BaseContainerEvent extends Event
{
    /**
     * @var Slim\Container
     */
    protected $container;

    protected $undefinedObject;

    public function __construct(Container $container = null, $param = null)
    {
        $this->container = $container;
        $this->undefinedObject = $param;
    }

    /**
     * @return \Slim\Container
     */
    public function getContainer()
    {
        return $this->container;
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
