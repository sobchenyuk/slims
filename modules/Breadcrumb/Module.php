<?php

namespace Modules\Breadcrumb;

use App\Source\BaseModule;
use App\Source\Factory\ModelsFactory;
use Modules\Breadcrumb\Source\BreadcrumbsBuilder;


class Module extends BaseModule
{
    const MODULE_NAME = 'breadcrumb';
    protected static $loaded = false;

    public $requireModules = ['core', 'sections'];

    public function afterInitialization(){

        parent::afterInitialization();

        $this->container->dispatcher->addListener('publiccontroller.render.before', function ($event) {
            
            $page = $event->getParams()->pageData['pageData'];

            if( $page instanceof \App\Models\Sections ){
                $path = $page->path;
                $url = $event->getContainer()->router->pathFor('page.s'.$page->id);
            }
            if( $page instanceof \App\Models\Pages && $page->category_id > 0){
                $path = ModelsFactory::getModel('sections')->find($page->category_id)['path'];
                $path .= $page->category_id;
                $url = $event->getContainer()->router->pathFor('page.sp'.$page->category_id, ['pageCode'=>$page->code]);
            }
            if( $page instanceof \App\Models\Pages && $page->category_id<=0){
                $path = '0'.\App\Models\Sections::PATH_DELIMITER;
                //$url = $event->getContainer()->router->pathFor('page.'.$page->id);
            }

            $bc = new BreadcrumbsBuilder($path);
            
            if( true ){
                $bc->parsePath()->addLastItem($url, $page->name);
            }

            $event->getParams()->pageData['breadcrumbs'] = $bc;
        });
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