<?php
$GLOBALS['startTime'] = microtime(true);

use SlimCMS\Modules\ModuleLoader;
use Slim\Container;
use SlimCMS\Modules\SModuleManager;
use SlimCMS\Helpers\ConfigWorker;
use SlimCMS\Factory\AppFactory;
use App\Source\Decorators\SlimCMS;

session_start();

define('ROOT_PATH', str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__ . '/../../') . '/'));

define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
define('SLIM_PATH', ROOT_PATH . 'src' . DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH . 'cache' . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', ROOT_PATH . 'vendor' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', ROOT_PATH . 'public' . DIRECTORY_SEPARATOR);
define('RESOURCE_PATH', ROOT_PATH . 'resource' . DIRECTORY_SEPARATOR);

define('MODULE_PATH', ROOT_PATH . 'modules' . DIRECTORY_SEPARATOR);

$classLoader = require VENDOR_PATH . 'autoload.php';

require SLIM_PATH . 'Helpers'.DIRECTORY_SEPARATOR.'functions.php';

/**
 * Load the configuration
 */
$config = array(
    'path.app' => APP_PATH,
    'path.root' => ROOT_PATH,
    'path.slim' => SLIM_PATH,
    'path.cache' => CACHE_PATH,
    'path.public' => PUBLIC_PATH,
    'path.module' => MODULE_PATH,
    'path.resource' => RESOURCE_PATH,
);

$clearCache = false;
if (isset($_REQUEST['clear_cache'])) {
    $clearCache = true;
}

/** include Config files */
$config += ConfigWorker::init($clearCache)->all();


if( !isset($config['slim']) ){
    $container = new Container(['debug' => true, 'use_log' => false, 'determineRouteBeforeAppMiddleware' => true, 'displayErrorDetails' => true]);
    $app = AppFactory::setInstance(new SlimCMS($container));
    ModuleLoader::bootEasyModule(new Modules\SystemInstaller\Module());
    return $app;
}

if ($config['slim']['settings']['debug']) {
    error_reporting(E_ALL ^ E_NOTICE);
}

$container = new Container($config['slim']);
$container->config = $config;

$app = AppFactory::setInstance(new SlimCMS($container));

$container->modules = new SModuleManager($clearCache);
$container->modules->loadModules(MODULE_PATH);

ModuleLoader::bootCore($container->modules->module('Core'));
ModuleLoader::bootLoadModules($container->modules);

return $app;