<?php

namespace SlimCMS\Contracts\Modules;

interface IModule
{
    /**
     * Run for install module
     * @return void
     */
    public function installModule();

    /**
     * Run for module remove
     * @return void
     */
    public function uninstallModule();

    /**
     * Instance app, and container in module
     * @return void
     */
    public function beforeInitialization();

    /**
     * Run if installed module(every loading system)
     * @return void
     */
    public function initialization();

    /**
     * Register route in slim framework
     * @return void
     */
    public function registerRoute();

    /**
     * Register DI container in slim framework
     * @return void
     */
    public function registerDi();

    /**
     * Register Middleware in slim framework
     * @return void
     */
    public function registerMiddleware();

    /**
     * Run after init module and register methods
     * @return void
     */
    public function afterInitialization();

    /**
     * Return loaded module status
     * @return bool
     */
    public function isInitModule();

    /**
     * Return module name
     * @return string
     */
    public static function getName();

    public static function setLoad();

    public static function getLoad();
}
