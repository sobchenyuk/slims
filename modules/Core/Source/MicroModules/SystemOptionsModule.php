<?php

namespace Modules\Core\Source\MicroModules;

use App\Source\BaseModule;
use App\Models\Options;
use App\Source\RouteSystem\AdminResource;
use App\Source\RouteSystem\AdminRouteCollection;
use Modules\Core\Source\Libs\Options\OptionsFacade;

class SystemOptionsModule extends BaseModule
{
    const MODULE_NAME = 'system_options';
    protected static $loaded = false;

    public function registerDi()
    {
        $this->container['systemOptions'] = function ($c) {
            return new OptionsFacade(Options::where('options_group_id', 1)->get());
        };
    }
    
    public function registerRoute()
    {
        AdminRouteCollection::add(new AdminResource('options', 'App\Controllers\Admin\OptionsController'));
        AdminRouteCollection::add(new AdminResource('group_options'));
    }

    public function installModule()
    {
        parent::installModule();

        $this->container->get('db')->schema()->create('options_group', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->char('active', 1)->default(1);
            $table->timestamps();
        });

        $this->container->get('db')->schema()->create('options', function($table) {
            $table->increments('id');
            $table->integer('options_group_id')->unsigned();
            $table->string('name', 255);
            $table->text('description');
            $table->string('value')->nullable();
            $table->string('type');
            $table->string('code');
            $table->string('values')->nullable();
            $table->string('frozen')->nullable();
            $table->timestamps();
            $table->unique('code');
            $table->index(['options_group_id', 'code']);
            $table->foreign('options_group_id')->references('id')->on('options_group');
        });

        $this->seed();
    }

    public function uninstallModule()
    {
        parent::uninstallModule();

        $this->container->get('db')->schema()->dropIfExists('options');
        $this->container->get('db')->schema()->dropIfExists('options_group');
    }

    protected function seed(){
        $this->container->get('db')->table('options_group')->insert([
            ["name" => "System", "description" => "System options", "active" => 1],
            ["name" => "Template \"main\"", "description" => "Options for base template", "active" => 1]
        ]);

        $this->container->get('db')->table('options')->insert([
            [
                "options_group_id" => 1,
                "name" => "Use email or login from authorize",
                "code" => "email_or_login",
                "description" => "Use email or login from authorize",
                "value" => "login",
                "frozen" => 0,
                "type" => "radio",
                "values" => '{"login":"login","email":"email"}',
            ],
            [
                "options_group_id" => 1,
                "name" => "Default controller",
                "code" => "default_controller",
                "description" => "System options",
                "value" => "PublicControllers",
                "frozen" => 0,
                "type" => "string",
                "values" => "",
            ],
            [
                "options_group_id" => 1,
                "name" => "Developers mode",
                "code" => "develop_mode",
                "description" => "Use admin from developers mode",
                "value" => 0,
                "frozen" => 1,
                "type" => "checkbox",
                "values" => "",
            ],
            [
                "options_group_id" => 1,
                "name" => "Freeze mode",
                "code" => "freeze_mode",
                "description" => "Use freeze mode from options table",
                "value" => 1,
                "frozen" => 1,
                "type" => "checkbox",
                "values" => "",
            ],
            [
                "options_group_id" => 2,
                "name" => "Product Version",
                "code" => "version",
                "description" => "Version SlimCMS",
                "value" => "0.2.0",
                "frozen" => 0,
                "type" => "string",
                "values" => "",
            ]
        ]);
    }
}
