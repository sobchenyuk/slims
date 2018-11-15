<?php

namespace SlimCMS\Modules;

use SlimCMS\Factory\AppFactory;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use SlimCMS\Contracts\Modules\IModuleManager;

/**
 *
 */
class SModuleManager implements IModuleManager
{
    public $moduleNamespace = "\\Modules";

    protected $path = MODULE_PATH;
    protected $cache;
    protected $modulesName = [];

    protected $filesystem;
    protected $moduleContainer;
    protected $cacheManager;

    /**
     * @param bool $cache
     */
    public function __construct($cache)
    {
        $this->filesystem = new Filesystem();
        $this->moduleContainer = new Container();
        $this->cache = !$cache;

        $container = AppFactory::getInstance()->getContainer();
        $container['cache'] = function () {
            $cacheContainer = new Container();

            $cacheContainer->singleton('files', function () {
                return new Filesystem();
            });
            $cacheContainer->singleton('config', function () {
                return AppFactory::getInstance()->getContainer()->config['cache'];
            });
            return new CacheManager($cacheContainer);
        };

        $this->cacheManager = $container->get('cache');
    }

    /**
     * Find and analyse the module folder
     * @params string $path
     */
    public function loadModules($path = "")
    {
        if ($path && $this->filesystem->isDirectory($path)) {
            $this->path = $path;
        }

        if (!$this->cache || !$this->cacheManager->has('folder.modules')) {
            $folders = $this->filesystem->directories($this->path);
            $this->cacheManager->forever('folder.modules', json_encode($folders));
        } else {
            $folders = json_decode($this->cacheManager->get('folder.modules'));
        }

        foreach ($folders as $folder) {
            $this->initModule($folder);
        }
    }

    /**
     * Get module by name
     * @param string $name
     * @return IModule|mixed
     */
    public function module($name)
    {
        return $this->moduleContainer->make($name);
    }

    /**
     * Get all modules in container
     * @return Container
     */
    public function getModules()
    {
        return $this->moduleContainer;
    }

    /**
     * Init proccess by one module
     * @param string $folder
     * @throws \Exception
     * @return void
     */
    protected function initModule($folder)
    {
        $moduleName = $this->filesystem->name($folder);
        if (!$this->cache || !$this->cacheManager->has('module.' . $moduleName . '.info')) {
            $config = $this->checkConfig($this->extModuleInfo($folder . DIRECTORY_SEPARATOR . 'config.json'));
            $info = $this->checkInfo($this->extModuleInfo($folder . DIRECTORY_SEPARATOR . 'info.json'), $moduleName);
            $data = ["info" => $info, "config" => $config];
            $this->cacheManager->forever('module.' . $moduleName . '.info', json_encode($data));
        } else {
            $data = json_decode($this->cacheManager->get('module.' . $moduleName . '.info'));
            $config = $data->config;
            $info = $data->info;
        }
        unset($data);

        $this->loadModule($moduleName, $config, $info);
    }

    /**
     * Get information for module
     * @param string $path
     * @return \stdClass|mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function extModuleInfo($path)
    {
        if ($this->filesystem->exists($path)) {
            return json_decode($this->filesystem->get($path));
        }
    }

    /**
     * Load module in container
     * @param string $name
     * @param \stdClass $config
     * @param \stdClass $info
     * @return bool
     * @throws \Exception
     */
    public function loadModule($name, $config, $info)
    {
        $ext = false;
        $baseClass = $this->moduleNamespace . '\\' . $name . '\\Module';
        $cl = $baseClass;

        if (!$config->params->installed ||
            !$config->params->active ||
            ($config->params->only_auth && true)
        ) {
            return false;
        }

        if ($this->moduleContainer->offsetExists($this->moduleNamespace . '\\' . $name))
            return false;

        if (isset($config->class_ext)) {
            $baseClass = $cl;
            $cl = $config->class_ext;
            $ext = true;
        }

        if (!class_exists($cl))
            throw new \Exception("Class \"$cl\" not found", 1);

        if ($ext) {
            $p = trim(get_parent_class($cl), '\\');
            $b = trim($baseClass, '\\');
            if ($p != $b)
                throw new \Exception("Class \"$cl\" not extend base class \"$baseClass\"", 1);
        }

        if (isset($config->dependeny) && is_array($config->dependeny)) {
            $this->checkDependecies($config->dependeny);
        }

        $this->moduleContainer->singleton($this->moduleNamespace . '\\' . $name, function () use ($info, $config, $cl) {
            $module = new $cl();
            $module->setInfo($info);
            $module->setConfig($config->params);
            return $module;
        });

        $this->moduleContainer->alias($this->moduleNamespace . '\\' . $name, $info->system_name);
        $this->modulesName[] = $info->system_name;

        if (isset($config->class_decorators) && is_array($config->class_decorators)) {
            $this->decoratorsInit($this->moduleNamespace . '\\' . $name, $config->class_decorators);
        }

        return true;
    }

    /**
     * Return all name modules in the container
     * @return array
     */
    public function keys()
    {
        return $this->modulesName;
    }

    /**
     * Check base parametr in config
     * @param \stdClass $config
     * @return mixed
     */
    protected function checkConfig($config)
    {
        $defClassConfig = ["installed", "active", "only_admin"];

        if (!isset($config->params))
            $config->params = new \stdClass();

        foreach ($defClassConfig as $type) {
            if (!isset($config->params->$type)) {
                $config->params->$type = false;
            }
        }

        return $config;
    }

    /**
     * Check base parametr in info
     * @param $info
     * @param $name
     * @return \stdClass
     */
    protected function checkInfo($info, $name)
    {
        if (!($info instanceof \stdClass)) {
            $info = new \stdClass();
        }

        if (!isset($info->system_name))
            $info->system_name = $name;

        return $info;
    }

    /**
     * Set decorators class by module
     * @param string $name
     * @param array $decorators
     * @throws \Exception
     */
    protected function decoratorsInit($name, array $decorators)
    {
        foreach ($decorators as $decorClass) {
            if (!class_exists($decorClass))
                throw new \Exception("Class decorator \"$decorClass\" - not found", 1);

            $this->moduleContainer->extend($name, function ($module) use ($decorClass) {
                return new $decorClass($module);
            });
        }
    }

    /**
     * Check dependencies for concret module initialized
     * @param array $dependeny
     * @throws \Exception
     */
    protected function checkDependecies(array $dependeny)
    {
        foreach ($dependeny as $moduleName) {
            if (!$this->moduleContainer->offsetExists($this->moduleNamespace . '\\' . $moduleName)) {
                $folder = $this->path . DIRECTORY_SEPARATOR . $moduleName;
                if (!$this->filesystem->isDirectory($folder))
                    throw new \Exception("Module \"$moduleName\" - not found", 1);

                $this->initModule($folder);

                if (!$this->moduleContainer->offsetExists($this->moduleNamespace . '\\' . $moduleName)) {
                    throw new \Exception("Module \"$moduleName\" - don't loaded. Please сheck whether the module is installed and enabled", 1);
                }
            }
        }
    }
}