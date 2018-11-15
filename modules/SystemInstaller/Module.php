<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 7/4/16
 * Time: 1:39 AM
 */

namespace Modules\SystemInstaller;

use App\Helpers\FileWorker;
use SlimCMS\Modules\ModuleLoader;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Helpers\RequestParams;
use App\Source\BaseModule;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

class Module extends BaseModule
{
    const MODULE_NAME = 'SystemInstaller';
    protected static $loaded = false;

    public function registerDi()
    {
        $this->container['view'] = function ($c) {
            $view = new Twig(APP_PATH.'templates');

            // Instantiate and add Slim specific extension
            $view->addExtension(new TwigExtension(
                $c['router'],
                $c['request']->getUri()
            ));

            $view->addExtension(new \Twig_Extension_Debug());

            return $view;
        };
    }

    public function registerRoute()
    {
        $this->app->get('/', function ($req, $res) {
            $res->getBody()->write("Please <a href=\"\install-system\">install</a> system");
        })->setName('home');

        $this->app->get('/install-system', function ($req, $res, $args) {
            return $this->view->render($res, 'admin\install.twig', [
                'step' => 1,
                'view' => '1',
            ]);
        })->setName('installer');

        $parentClass = $this;
        $this->app->post('/install-system', function ($req, $res, $args)use($parentClass) {
            $request = new RequestParams($req);
            $_allParams = $request->all();
            unset($_allParams['step']);
            $allParams = array();
            if( 'finish' == $request->post('step') ){
                $parentClass->installSystem($_allParams);
                return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('home'));
            }
            foreach($_allParams as $k=>$item){
                if(is_array($item)){
                    foreach($item as $_k => $_v){
                        $allParams[$k."[$_k]"]=$_v;
                    }
                } else {
                    $allParams[$k]=$item;
                }
            }
            return $this->view->render($res, 'admin\install.twig', [
                'prevData' => $allParams,
                'step' => $request->post("step"),
                'view' => $request->post("step"),
            ]);
        })->setName('installer-step');

        $this->app->post('/install-system/checkdb', function($req, $res, $args){
            $request = new RequestParams($req);
            if ($request->isXhr()) {
                $checkdb = false;
                $config = [];

                if( $request->post('dbType') == 'mysql' ){
                    $config = [
                        'driver' => 'mysql',
                        'host' => $request->post('dbHost'),
                        'database' => $request->post('dbName'),
                        'username' => $request->post('dbLogin'),
                        'password' => $request->post('dbPassword'),
                        'charset'  => 'utf8',
                        'collation' => 'utf8_general_ci',
                        'prefix' => ''
                    ];
                    $checkdb = true;
                }elseif( $request->post('dbType') == 'sqlite' ){
                    if( !$request->post('dbFileName') ){
                        $data = array('type' => 'error', 'msg' => "Please insert DB name");
                        return $res->withJson($data);
                    }
                    $file = RESOURCE_PATH.'database/'.strtolower($request->post('dbFileName')).'.sqlite';
                    if( file_exists($file) ){
                        $data = array('type' => 'error', 'msg' => "File exist, please insert other DB name");
                    } else {
                        $data = array('type' => 'success', 'msg' => "Step the next stage");
                        $checkdb = false;
                    }
                    $config = [
                        'driver'   => 'sqlite',
                        'database' => $file,
                        'prefix'   => ''
                    ];
                }

                if( $checkdb ){
                    $capsule = new Capsule();
                    $capsule->addConnection($config);
                    $capsule->setAsGlobal();
                    $capsule->bootEloquent();
                    try{
                        $capsule->schema()->hasTable("options");
                        $data = array('type' => 'success', 'msg' => "Step the next stage");
                    }catch (\Exception $e){
                        $data = array('type' => 'error', 'msg' => "DB not exist");
                    }
                }

                return $res->withJson($data);
            } else {
                $data = array('type' => 'error', 'msg' => "Request type error");
                return $res->withJson($data);
            }
        });
    }

    protected function installSystem($arParams)
    {
        $arConfig = [];

        $dbConfig = [];
        if( $arParams['dbType'] == 'mysql' ){
            $dbConfig[$arParams['dbType']] = [
                'driver' => $arParams['dbType'],
                'host' => $arParams['dbHost'],
                'database' => $arParams['dbName'],
                'username' => $arParams['dbLogin'],
                'password' => $arParams['dbPassword'],
                'charset'  => 'utf8',
                'collation' => 'utf8_general_ci',
                'prefix' => ''
            ];
        }elseif($arParams['dbType'] == 'sqlite'){
            $file = RESOURCE_PATH.'database/'.strtolower($arParams['dbFileName']).'.sqlite';
            FileWorker::saveFile($file, '');
            $dbConfig[$arParams['dbType']] = [
                'driver'   => $arParams['dbType'],
                'database' => $file,
                'prefix'   => '',
            ];
        };
        $arConfig['db'] = $dbConfig;

        $arConfig['slim']['settings'] = [
            'db_driver' => $arParams['dbType'],
            'displayErrorDetails' => $arParams['displayErrorDetails'],
            'debug' => $arParams['debug'],
            'use_log' => $arParams['use_log'],
            'log_system' => $arParams['log_system'],
            'log_filename' => 'app.log',
            'register_log' => $arParams['register_log'],
            'determineRouteBeforeAppMiddleware' => true,
            'protect_double_route_register' => true,
        ];
        $arConfig['view'] = [
            'template_path' => APP_PATH.'templates',
            'twig' => [
                'cache' => CACHE_PATH.'twig',
                'debug' => $arParams['debug'],
                'auto_reload' => $arParams['debug'],
            ]
        ];
	$arConfig['cache'] = array(
            'cache.default' => 'files',
            'cache.stores.files' => array(
                'driver' => 'file',
                'path' => CACHE_PATH.'slimcms'
            )
        );

        FileWorker::savePhpReturnFile(APP_PATH.'config/local/autoconfig.php', $arConfig);

        $this->registerDB($dbConfig[$arParams['dbType']]);

        if(isset($this->specialData['coreClassName']) && class_exists($this->specialData['coreClassName'])){
            $cl = $this->specialData['coreClassName'];
            ModuleLoader::install(new $cl());
        } else {
            ModuleLoader::install(new \Modules\Core\Module());
        }

        if( $arParams['modules'] && is_array($arParams['modules']) ){
            foreach($arParams['modules'] as $module){
                $cl = '\\Modules\\'.$module.'\\Module';
                if( class_exists($cl) ){
                    ModuleLoader::install(new $cl());
                }
            }
        }
    }

    protected function registerDB($config){
        $capsule = new Capsule();
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $this->container['db'] = function () {
            return new Capsule();
        };
    }
}