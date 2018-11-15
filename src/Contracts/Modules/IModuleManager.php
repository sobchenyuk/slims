<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 6/11/16
 * Time: 4:10 PM
 */
namespace SlimCMS\Contracts\Modules;

interface IModuleManager
{
    public function loadModules($path = "");

    public function module($name);

    public function getModules();

    public function keys();

    public function loadModule($name, $config, $info);
}