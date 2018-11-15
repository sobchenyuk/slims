<?

require_once __DIR__ . '/../vendor/autoload.php';

$GLOBALS['startTime'] = microtime(true);

session_start();

define('ROOT_PATH', str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__ . '/../') . '/'));

define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
define('SLIM_PATH', ROOT_PATH . 'src' . DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH . 'cache' . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', ROOT_PATH . 'vendor' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', ROOT_PATH . 'public' . DIRECTORY_SEPARATOR);
define('RESOURCE_PATH', ROOT_PATH . 'resource' . DIRECTORY_SEPARATOR);

define('MODULE_PATH', ROOT_PATH . 'modules' . DIRECTORY_SEPARATOR);

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