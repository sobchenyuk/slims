<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 7/3/16
 * Time: 1:10 AM
 */

namespace App\Source\Decorators;


use App\Source\Factory\AppFactory;
use Slim\App;

/**
 * Class SlimCMS
 * @package App\Source\Decorators
 */
class SlimCMS extends App
{
    /**
     * @param bool|false $silent
     */
    public function run($silent = false)
    {
        $this->onStar();

        parent::run($silent);

        $this->onFinish();
    }

    /**
     *
     */
    protected function onStar()
    {
        $event = new \App\Source\Events\BaseAppEvent($this);
        if($this->getContainer()->offsetExists('dispatcher')){
            $this->getContainer()->dispatcher->dispatch('app.beforeRun', $event);
        }
    }

    /**
     *
     */
    protected function onFinish()
    {
        if( !$this->getContainer()->offsetExists('logger') ) return;

        $event = new \App\Source\Events\BaseLoggerEvent($this->getContainer()->get('logger'));
        if($this->getContainer()->offsetExists('dispatcher')) {
            $this->getContainer()->dispatcher->dispatch('app.afterRun', $event);
        }
    }
}
