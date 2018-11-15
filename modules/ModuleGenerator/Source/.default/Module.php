<?php

namespace Modules\%system_name%;

use App\Source\AModule;

class Module extends AModule
{
    const MODULE_NAME = '%system_name%';
    protected static $_loaded = false;

    public $requireModules = ['core'];

    public function installModule()
    {
        parent::installModule();
        $this->saveConfigForModule(self::class, ["installed"=>true, "active"=>true]);
    }

    public function uninstallModule()
    {
        parent::uninstallModule();
        $this->saveConfigForModule(self::class, ["installed"=>false, "active"=>false]);
    }

    public function beforeInitialization()
    {
        parent::beforeInitialization();
    }

    public function initialization()
    {}

    public function registerDi()
    {}

    public function registerRoute()
    {}

    public function registerMiddleware()
    {}

    public function afterInitialization(){
        parent::afterInitialization();
    }
}