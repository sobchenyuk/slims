<?php

namespace Modules\Core\Source\MicroModules;

use App\Models\Pages;
use App\Source\BaseModule;
use App\Source\RouteSystem\PageResource;
use App\Source\RouteSystem\PageRouteCollection;


class PublicModule extends BaseModule
{
    const MODULE_NAME = 'public_cms';
    protected static $loaded = false;

    public function registerRoute()
    {
        $pages = Pages::where('active', 1)->orderBy('id', 'asc')->get()->toArray();

        if( empty($pages) )
            return;

        $this->container->get('router')->removeNamedRoute('home');

        while ($page = array_shift($pages)) {
            $url = $page['url_prefix'].'/'.$page['code'];

            $controller = 'detailAction';

            if( $page['code'] == "" )
                $controller = 'homeAction';

            if( !$page['category_id'] )
                PageRouteCollection::add(new PageResource($url, $controller, $page['id']));
        }
    }

    public function afterInitialization(){
        parent::afterInitialization();

        $this->menuCreator();

        $this->container->dispatcher->addListener('app.beforeRun', function ($event){
            PageRouteCollection::register($event->getApp());
        }, 2000);
    }

    protected function menuCreator(){
        $this->container->dispatcher->addListener('publiccontroller.menu.logic', function ($event) {
            $items = Pages::where('show_in_menu', 1)->where('active', 1)->orderBy('sort', 'asc')->get();

            $name = '';
            if($route = $event->getParams()->request->getAttribute('route'))
                $name = $route->getName();

            $menu = $event->getParams()->menu;
            foreach ($items as $item) {
                $menu[] = [
                    'name' => $item->name_for_menu,
                    'current' => (bool)($name=='page.'.$item->id),
                    'section' => $item->category_id,
                    'code' => $item->code,
                    'id' => $item->id,
                    'url' => 'page.'.$item->id,
                ];
            }
            
            $event->getParams()->menu = $menu;
        });
    }

    public function installModule()
    {
        parent::installModule();

        $this->container->get('db')->schema()->create('pages', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('sort')->default(100)->nullable();
            $table->string('url_prefix')->nullable();
            $table->text('preview_text')->nullable();
            $table->text('detail_text')->nullable();
            $table->string('preview_picture')->nullable();
            $table->string('detail_picture')->nullable();
            $table->integer('show_in_menu')->default(0)->nullable();
            $table->string('name_for_menu')->nullable();
            $table->integer('active')->default(1);
            $table->timestamps();
        });

        $this->seed();
    }

    public function uninstallModule()
    {
        parent::uninstallModule();

        $this->container->get('db')->schema()->dropIfExists('pages');
    }

    protected function seed()
    {
        $this->container->get('db')->table('pages')->insert(
            [
                "name" => "Home page",
                "code" => "",
                "sort" => 100,
                "preview_text" => "<p>Preview text for home page</p>",
                "detail_text" => "<p>Detail text for home page</p>",
                "show_in_menu" => 1,
                "name_for_menu" => "Home",
                "active" => 1,
            ],
            [
                "name" => "About page",
                "code" => "about",
                "sort" => 100,
                "preview_text" => "<p>Preview text for about page</p>",
                "detail_text" => "<p>Detail text for about page</p>",
                "show_in_menu" => 1,
                "name_for_menu" => "About",
                "active" => 1,
            ],
            [
                "name" => "Contacts page",
                "code" => "contacts",
                "url_prefix" => "about",
                "sort" => 100,
                "preview_text" => "<p>Preview text for contacts page</p>",
                "detail_text" => "<p>Detail text for contacts page</p>",
                "show_in_menu" => 1,
                "name_for_menu" => "Contacts",
                "active" => 1,
            ]
        );
    }
}
