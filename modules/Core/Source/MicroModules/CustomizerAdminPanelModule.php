<?php

namespace Modules\Core\Source\MicroModules;

use App\Source\BaseModule;
use Modules\Core\Source\Libs\Middleware\ItemPerPageMiddleware;
use Modules\Core\Source\Libs\Middleware\LastPagePaginatorMiddleware;
use Modules\Core\Source\Libs\Middleware\OrderTypeMiddleware;

class CustomizerAdminPanelModule extends BaseModule
{
    const MODULE_NAME = 'customizer_admin_panel';
    protected static $loaded = false;

    public function registerRoute()
    {
    	$this->app->options('/ajax', 'App\Controllers\Admin\UniversalAjaxController:update')->add('Modules\Core\Source\Libs\Middleware\CheckAjaxMiddleware')->setName('ajax.custom.field');
    }

    public function registerMiddleware()
    {
        $this->app->add(new LastPagePaginatorMiddleware($this->container));
    	$this->app->add(new ItemPerPageMiddleware($this->container));
        $this->app->add(new OrderTypeMiddleware($this->container));
    }

    public function afterInitialization()
    {
        parent::afterInitialization();

        $this->container->dispatcher->addListener('middleware.itemparpage.after', function ($event) {
            $page = new LastPagePaginatorMiddleware($event->getContainer());
            $page->setOption(1, $event->getParams()['allParams']);
        });
    }

    public function installModule()
    {
        parent::installModule();

        $this->container->get('db')->schema()->create('user_views_settings', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('group');
            $table->string('value')->nullable();
            $table->string('option_type')->nullable();
            $table->string('code');
            $table->index(['user_id', 'group', 'code']);
        });
    }

    public function uninstallModule()
    {
        parent::uninstallModule();

        $this->container->get('db')->schema()->dropIfExists('user_views_settings');
    }
}
