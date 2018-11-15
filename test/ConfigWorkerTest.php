<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 12/29/16
 * Time: 2:05 AM
 */

namespace test;

use Noodlehaus\Config;
use SlimCMS\Helpers\ConfigWorker;

class ConfigWorkerTest extends \PHPUnit_Framework_TestCase
{
    protected $config = [
        "environment" => "test",
        "configFolderName" => "config",
        "compileFolderName" => "config",
        "blockCacheFile" => ".blockTestCache"
    ];

    protected $testConfig = ["slim" => [
        "settings" => [
            "option_1" => true
        ],
        "custom" => [
            "option_1" => true
        ]
    ]
    ];

    public function testCacheInit()
    {
        $configW = ConfigWorker::init(false, $this->config);
        $objFolders = ConfigWorker::getEnvFiles();
        $this->assertInstanceOf(Config::class, $configW);
        $this->assertInstanceOf(\stdClass::class, $objFolders);

        $this->assertEquals($configW->all(), $this->testConfig);

        $this->assertObjectHasAttribute("environment", $objFolders);
        $this->assertObjectHasAttribute("baseConfigPath", $objFolders);
        $this->assertObjectHasAttribute("realConfigPath", $objFolders);
        $this->assertObjectHasAttribute("cacheConfigPath", $objFolders);
        $this->assertObjectHasAttribute("cacheConfigFile", $objFolders);
        $this->assertObjectHasAttribute("blockConfigCache", $objFolders);
        $this->assertFileExists($objFolders->realConfigPath);
        $this->assertFileExists($objFolders->cacheConfigFile);

        $getConfig = ConfigWorker::getConfig();
        $this->assertEquals($configW, $getConfig);

        ConfigWorker::clearCache();
        ConfigWorker::clearInit();
        $objFolders1 = ConfigWorker::getEnvFiles();
        $this->assertNull($objFolders1);
    }

    public function testBlockCacheFile()
    {
        $folder = CACHE_PATH . $this->config["compileFolderName"] . DIRECTORY_SEPARATOR;
        $blockFile = fopen($folder.$this->config['blockCacheFile'], "w");
        fclose($blockFile);
        ConfigWorker::init(false, $this->config);
        $objFolders = ConfigWorker::getEnvFiles();

        $this->assertTrue(unlink($objFolders->cacheConfigPath.$this->config['blockCacheFile']));
        $this->assertTrue($objFolders->blockConfigCache);
        $this->assertFileNotExists($objFolders->cacheConfigFile);
        ConfigWorker::clearCache();
        ConfigWorker::clearInit();
    }

    public function testRecreateCache()
    {
        $configW = ConfigWorker::init(false, $this->config);
        $objFolders = ConfigWorker::getEnvFiles();
        $this->assertInstanceOf(Config::class, $configW);
        $this->assertInstanceOf(\stdClass::class, $objFolders);

        $this->assertEquals($configW->all(), $this->testConfig);

        $this->assertObjectHasAttribute("environment", $objFolders);
        $this->assertObjectHasAttribute("baseConfigPath", $objFolders);
        $this->assertObjectHasAttribute("realConfigPath", $objFolders);
        $this->assertObjectHasAttribute("cacheConfigPath", $objFolders);
        $this->assertObjectHasAttribute("cacheConfigFile", $objFolders);
        $this->assertObjectHasAttribute("blockConfigCache", $objFolders);

        $this->assertFileExists($objFolders->cacheConfigFile);
        file_put_contents($objFolders->cacheConfigFile, "");

        ConfigWorker::clearInit();
        $configW = ConfigWorker::init(true, $this->config);
        $this->assertInstanceOf(Config::class, $configW);
        $this->assertInstanceOf(\stdClass::class, $objFolders);

        $this->assertEquals($configW->all(), $this->testConfig);
        ConfigWorker::clearCache();
        ConfigWorker::clearInit();
    }
}
