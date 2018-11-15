<?php
namespace App\Source\Events;

use Modules\Core\Source\Libs\Logger\LoggerSystem;
use Symfony\Component\EventDispatcher\Event;

class BaseLoggerEvent extends Event
{

    /**
     * @var Modules\Core\Source\Libs\Logger\LoggerSystem
     */
    protected $logger;

    protected $undefinedObject;

    public function __construct($logger, $param = null)
    {
        $this->logger = $logger;
        $this->undefinedObject = $param;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function getParam()
    {
        return $this->undefinedObject;
    }
}
