<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 6/11/16
 * Time: 9:18 PM
 */

namespace SlimCMS\Factory;


use Slim\App;

class AppFactory
{
    protected static $app;

    public static function setInstance(App $app){
        if( !(self::$app instanceof App) ){
            self::$app = $app;
        }

        return self::getInstance();
    }

    public static function getInstance($name = false)
    {
        if( !$name )
            return self::$app;

        if(self::$app->getContainer()->offsetExists($name))
            return self::$app->getContainer()->get($name);
    }
}