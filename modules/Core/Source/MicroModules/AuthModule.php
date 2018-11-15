<?php

namespace Modules\Core\Source\MicroModules;

use App\Source\BaseModule;

class AuthModule extends BaseModule
{
    const MODULE_NAME = 'auth';
    protected static $loaded = false;

    public function registerRoute()
    {
        $this->app->group('/auth', function () {
            $this->get('/login', 'App\Controllers\Admin\AuthController:login')->setName('login');
            $this->post('/login', 'App\Controllers\Admin\AuthController:doLogin')->setName('doLogin');
            $this->get('/logout', 'App\Controllers\Admin\AuthController:logout')->setName('logout');
        });
    }

    public function installModule()
    {
        parent::installModule();

        $this->container->get('db')->schema()->create('groups', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->char('active', 1)->default(1);
            $table->timestamps();
        });

        $this->container->get('db')->schema()->create('users', function($table) {
            $table->increments('id');
            $table->string('email', 120)->unique();
            $table->string('login', 70)->unique();
            $table->string('password', 80);
            $table->char('active', 1)->default(1);
            $table->integer('group_id')->default(3)->unsigned();
            $table->timestamps();
            $table->index(['email', 'login']);
            $table->foreign('group_id')->references('id')->on('groups');
        });

        $this->seed();
    }

    public function uninstallModule()
    {
        parent::uninstallModule();

        $this->container->get('db')->schema()->dropIfExists('users');
        $this->container->get('db')->schema()->dropIfExists('groups');
    }

    protected function seed()
    {
        $this->container->get('db')->table('groups')->insert([
            ["name" => "Administrators", "description" => "Administrators system", "active" => 1],
            ["name" => "Users", "description" => "registered user in site, no perm to admin panel", "active" => 1],
            ["name" => "Guests", "description" => "All other users", "active" => 1],
        ]);
        $this->container->get('db')->table('users')->insert(
            [
                "email" => "admin@net.net",
                "login" => "admin",
                "password" => '$2y$12$YT0yYV1iUehdx0eymGIEfOR1fJ7Zo3EH7T/extuNFjq.0H942yNYK',
                "active" => 1,
                "group_id" => 1,

            ]
        );
    }
}
