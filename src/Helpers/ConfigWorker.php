<?php

namespace SlimCMS\Helpers;

use Noodlehaus\Config;

/**
 * Class ConfigWorker
 * @package App\Helpers
 */
class ConfigWorker
{
    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @var \stdClass
     */
    protected static $folders;


    /**
     * Initialization method and return config array
     * @param array $arConfig
     * @param bool|false $recreateCache
     * @return array
     */
    public static function init($recreateCache = false, array $arConfig = [])
    {
        self::loadEnvFiles($arConfig);

        if (self::$config == [])
            self::cacheConfig($recreateCache);

        return self::$config;
    }

    /**
     * Load configuration files
     * @params array $arConfig
     * @param array $arConfig
     * @return null|void
     */
    public static function loadEnvFiles(array $arConfig = [])
    {
        if (is_object(self::$folders))
            return;

        $arDefault = [
            "environment" => "local",
            "configFolderName" => "config",
            "compileFolderName" => "config",
            "blockCacheFile" => ".blockCache"
        ];

        $arConfig = array_merge($arDefault, $arConfig);

        self::$folders = new \stdClass();

        if (is_file(ROOT_PATH . '.env')) {
            self::$folders->environment = file_get_contents(ROOT_PATH . '.env');
        }

        if( !isset(self::$folders->environment) || !self::$folders->environment ){
            self::$folders->environment = $arConfig["environment"];
        }

        self::$folders->baseConfigPath = APP_PATH . $arConfig["configFolderName"] . DIRECTORY_SEPARATOR;
        self::$folders->realConfigPath = self::$folders->baseConfigPath . self::$folders->environment . DIRECTORY_SEPARATOR;

        if (!is_dir(self::$folders->realConfigPath)) {
            self::$folders->realConfigPath = self::$folders->baseConfigPath . $arConfig["environment"] . DIRECTORY_SEPARATOR;
        }

        self::$folders->cacheConfigPath = CACHE_PATH . $arConfig["compileFolderName"] . DIRECTORY_SEPARATOR;
        self::$folders->cacheConfigFile = self::$folders->cacheConfigPath . self::$folders->environment . ".php";
        self::$folders->blockConfigCache = is_file(self::$folders->cacheConfigPath . $arConfig["blockCacheFile"]);
    }

    /**
     * @param bool|false $reCreate
     */
    protected static function cacheConfig($reCreate = false)
    {
        if (
            $reCreate ||                                // make new cache file
            self::$folders->blockConfigCache ||         // block cache file
            !is_file(self::$folders->cacheConfigFile)   // no cache file
        ) {
            if (!is_dir(self::$folders->realConfigPath) || !glob(self::$folders->realConfigPath . DIRECTORY_SEPARATOR . '*')) {
                self::$config = new Config([]);
                return;
            }
            self::$config = new Config(self::$folders->realConfigPath);

            if (!self::$folders->blockConfigCache) {
                self::makeCacheConfig(self::$config->all());
            }
        } else {
            self::$config = new Config(self::$folders->cacheConfigFile);
        }
    }

    /**
     * @param mixed $allConfig
     */
    protected static function makeCacheConfig($allConfig)
    {
        $strData = var_export($allConfig, true);
        $content = sprintf('<?php ' . PHP_EOL . PHP_EOL . 'return %s;', $strData);
        if (!is_dir(self::$folders->cacheConfigPath)) {
            mkdir(self::$folders->cacheConfigPath);
        }
        file_put_contents(self::$folders->cacheConfigFile, $content);
    }

    /**
     * Get loaded config
     * @return array
     */
    public static function getConfig()
    {
        return self::$config;
    }

    /**
     * Clear Env variable
     */
    public static function clearInit()
    {
        self::$folders = null;
        self::$config = null;
    }

    public function clearCache()
    {
        if( self::$folders->cacheConfigFile && is_file(self::$folders->cacheConfigFile) )
            unlink(self::$folders->cacheConfigFile);
    }

    /**
     * @return \stdClass
     */
    public static function getEnvFiles()
    {
        return self::$folders;
    }
}