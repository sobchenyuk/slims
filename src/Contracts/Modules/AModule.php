<?php

namespace SlimCMS\Contracts\Modules;

use SlimCMS\Factory\AppFactory;
use Illuminate\Support\Str;

abstract class AModule implements IModule
{
    protected $container;
    protected $app;

    private $config;
    private $info;

    protected $specialData;

    public $requireModules = ['Core'];

    public function __construct($data = null)
    {
        $c = get_called_class();
        if (!$c::MODULE_NAME) {
            throw new \Exception('Constant MODULE_NAME is not defined on subclass ' . get_class($c));
        }

        if (!isset(static::$loaded)) {
            throw new \Exception('Protected static variable $loaded is not defined on subclass ' . $c);//static::$_loaded = false;
        }

        $this->specialData = $data;
    }

    public function beforeInitialization()
    {
        $this->app = AppFactory::getInstance();
        $this->container = $this->app->getContainer();
    }

    public function initialization()
    {
    }

    public function afterInitialization()
    {
        static::setLoad();
    }

    public function isInitModule()
    {
        return (bool)static::getLoad();
    }

    public static function getName()
    {
        return static::MODULE_NAME;
    }

    public function installModule()
    {
        $this->beforeInitialization();
    }

    public function uninstallModule()
    {
        $this->beforeInitialization();
    }

    public function registerRoute()
    {
    }

    public function registerMiddleware()
    {
    }

    public function registerDi()
    {
    }

    protected function saveConfigForModule($class, array $arData)
    {
        $file = MODULE_PATH . Str::ucfirst($class::MODULE_NAME) . "/config.json";
        $arConfigData = new \stdClass();
        if (file_exists($file)) {
            $arConfigData = json_decode(file_get_contents($file));
        }
        foreach ($arData as $key => $item) {
            $key = strtolower($key);
            $arConfigData->$key = $item;
        }

        file_put_contents($file, json_encode($arConfigData, JSON_PRETTY_PRINT));
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setConfig($config)
    {
        if( is_object($config) )
            $this->config = $config;
    }

    public function setInfo($info)
    {
        if ( is_object($info) )
            $this->info = $info;
    }

    public static function getLoad()
    {
        return static::$loaded;
    }

    public static function setLoad()
    {
        static::$loaded = true;
    }
}