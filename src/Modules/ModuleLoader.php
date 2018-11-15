<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 6/11/16
 * Time: 8:52 PM
 */

namespace SlimCMS\Modules;

//use Pimple\Container;
use Illuminate\Container\Container;
use Noodlehaus\Exception\ParseException;
use Illuminate\Database\Capsule\Manager as Capsule;
//use App\Source\Events\BaseAppEvent;
//use App\Source\Events\BaseLoggerEvent;
use SlimCMS\Contracts\Modules\IModuleManager;
use SlimCMS\Factory\AppFactory;
use SlimCMS\Contracts\Modules\IModule;
use App\Source\Interfaces\IModuleLoader;
use App\Helpers\SessionManager as Session;

class ModuleLoader implements IModuleLoader
{
    protected static $loadedModules = [];
    protected static $moduleContainer;
    protected static $coreLoaded = false;

    public static function install(IModule $module){
        self::checkDbConnection();
        $module->installModule();
    }

    public static function uninstall(IModule $module){
        self::checkDbConnection();
        $module->uninstallModule();
    }

    protected static function checkDbConnection(){
        $container = AppFactory::getInstance()->getContainer();

        if( $container->offsetExists('db') ){
            return;
        }

        $config = $container->config;
        $settings = $container->settings;

        $capsule = new Capsule();

        $capsule->addConnection($config['db'][$settings['db_driver']]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $container['db'] = function () {
            return new Capsule();
        };
    }

    public static function bootCore(IModule $module)
    {
        if(!preg_match("/core/sui", $module->getName())){
            throw new ParseException("No load module".$module->getName()." - is don't core module group");
        }

        self::initProcess($module);

        if($module->isInitModule()){
            self::$loadedModules[$name] = $name;
            self::$coreLoaded = true;
        }
    }

    public static function bootLoadModules(IModuleManager $moduleContainer)
    {
        if( !self::$coreLoaded )
            return false;

        foreach($moduleContainer->keys() as $name){
            self::bootModuleContainer($moduleContainer->module($name));
        }
    }

    public static function bootEasyModule(IModule $module, $name = '')
    {
        if( !$name )
            $name = $module->getName();

        self::initProcess($module);

        self::$loadedModules[$name] = $name;
    }

    protected static function checkDependency($arDependency=false)
    {
        if(!$arDependency || !is_array($arDependency))
            return;
        foreach ($arDependency as $name) {
            if(self::$loadedModules[$name])
                continue;

            if(self::$moduleContainer[$name]){
                if( !self::$moduleContainer[$name]->config->installed ){
                    continue;
                }
                if( !self::$moduleContainer[$name]->config->active ){
                    AppFactory::getInstance('logger')->info("Module \"$name\" not active");
                    continue;
                }
                self::bootModuleContainer(self::$moduleContainer[$name]);
            }
            else
                AppFactory::getInstance('logger')->error("Can't find module \"$module\" in container");
        }
    }

    public static function initializationProcess(IModule $module, $name)
    {
        self::bootEasyModule($module, $name);
    }

    protected static function bootModuleContainer($module)
    {
        if( $module->only_auth && !Session::get('auth')){
            return;
        }

        if( $module->isInitModule() ){
            self::$loadedModules[$module->system_name] = $module->system_name;
            return;
        }

        self::checkDependency($module->dependeny);

        self::initializationProcess($module, $module->system_name);
    }

    /**
     * @param IModule $module
     */
    protected static function initProcess(IModule $module)
    {
        $module->beforeInitialization();
        $module->initialization();
        $module->registerRoute();
        $module->registerDi();
        $module->registerMiddleware();
        $module->afterInitialization();
        $module->setLoad();
    }
}