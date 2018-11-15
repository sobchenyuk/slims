<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 6/11/16
 * Time: 8:55 PM
 */
namespace App\Source\Interfaces;

use SlimCMS\Contracts\Modules\IModule;
use SlimCMS\Contracts\Modules\IModuleManager;

interface IModuleLoader
{
    public static function bootCore(IModule $module);

    public static function bootLoadModules(IModuleManager $moduleContainer);

    public static function bootEasyModule(IModule $module);
}