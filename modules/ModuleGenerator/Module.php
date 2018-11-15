<?php

namespace Modules\ModuleGenerator;

use App\Source\BaseModule;
use App\Source\Composite\Menu;
use App\Helpers\SessionManager;

class Module extends BaseModule
{
    const MODULE_NAME = 'ModuleGenerator';
    protected static $loaded = false;

    public $requireModules = ['Core'];

    public function beforeInitialization()
    {
        parent::beforeInitialization();
    }

    public function initialization()
    {
        $item = new Menu('Generator new module', [
            'menu_name' => 'developers.generator_module',
            'url' => '/admin/generate_module',
            'link_attr' => [
                'icon' => 'fa fa-ban fa-fw'
            ],
            'meta_attr' => [
                'onlyDevelopersMode' => true,
            ],
        ]);
        $this->container->get('adminMenuLeft')->getByName('section.only_developers')->add($item);
    }

    public function registerRoute()
    {
        $this->adminPanelRouteRegister();
    }

    public function afterInitialization(){
        parent::afterInitialization();
    }

    protected function adminPanelRouteRegister(){
        if( SessionManager::has('auth') && SessionManager::get('auth') && $this->container->systemOptions->isDevMode()){
            $this->app->get('/admin/generate_module', 'App\Controllers\Admin\ModuleGenerator:index')->setName('developers.module.generator');
            $this->app->post('/admin/generate_module', 'App\Controllers\Admin\ModuleGenerator:doAdd')->setName('developers.module.generator.add');
        }
    }

    public function installModule()
    {
        parent::installModule();

        $this->saveConfigForModule(self::class, ["params" => ["installed"=>true, "active"=>true]]);
    }

    public function uninstallModule()
    {
        parent::uninstallModule();

        $this->saveConfigForModule(self::class, ["params" => ["installed"=>false, "active"=>false]]);
    }
}