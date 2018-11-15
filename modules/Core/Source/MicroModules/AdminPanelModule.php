<?php

namespace Modules\Core\Source\MicroModules;

use App\Source\BaseModule;
use App\Source\Composite\Menu;

use App\Source\RouteSystem\AdminResource;
use App\Source\RouteSystem\AdminRouteCollection;
use App\Helpers\SessionManager as Session;
use Modules\Core\Source\Libs\Middleware\AuthMiddleware;

class AdminPanelModule extends BaseModule
{
    const MODULE_NAME = 'admin_panel';
    protected static $loaded = false;

    public function registerDi()
    {
    	$this->container['adminMenuLeft'] = function ($c) {
		    return new Menu('adminMenuLeft');
		};

        $this->container['adminMenuTop'] = function ($c) {
            return new Menu('adminMenuTop');
        };
    }

    public function registerMiddleware()
    {
    }

    public function registerRoute()
    {
        $this->adminPanelRouteRegister();

        $this->app->group('/admin', function () {
            $this->get('/', function($req, $res){
                return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('dashboard'));
            });
            $this->get('/dashboard', 'App\Controllers\Admin\DashboardController:index')->setName('dashboard');

            $this->get('/phpinfo', 'App\Controllers\Admin\DeveloperController:phpinfo')->setName('phpinfo');

            if( !Session::has('auth') || !Session::get('auth') ){
                $this->get('/{page:.*}', function($req, $res){
                return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('dashboard'));
                });
            }
        })->add( new AuthMiddleware() );
    }

    public function afterInitialization(){
        parent::afterInitialization();

        $this->adminPanelMenuRegister();

        $this->container->dispatcher->addListener('app.beforeRun', function ($event){
            $event->getApp()->group('/admin', function () {
                AdminRouteCollection::register($this);
            })->add( new AuthMiddleware() );
        }, -980);
    }

    protected function adminPanelRouteRegister(){
        AdminRouteCollection::add(new AdminResource('pages'));
        AdminRouteCollection::add(new AdminResource('users'));
        AdminRouteCollection::add(new AdminResource('groups'));
    }

    protected function adminPanelMenuRegister(){
        $item = new Menu('Dashboard', [
            'url' => '/admin/dashboard',
            'link_attr' => [
                'icon' => 'fa fa-dashboard fa-fw'
            ],
            'meta_attr' => [
                'onlyDevelopersMode' => false,
                'sort' => 100
            ]
        ]);
        
        $this->container->get('adminMenuLeft')->add($item);

        $item = new Menu('Pages',[
            'menu_name' => 'section.pages',
            'url' => '#',
            'link_attr' => [
                'icon' => 'fa fa-list-alt fa-fw'
            ],
            'meta_attr' => [
                'onlyDevelopersMode' => false,
                'sort' => 200
            ],
            'sub_menu' => [
                new Menu('Show all pages', [
                    'menu_name' => 'page.list',
                    'url' => '/admin/pages',
                    'link_attr' => [
                        'icon' => 'fa fa-file-o fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                    ],
                ]),
                new Menu('Add new page', [
                    'menu_name' => 'page.add',
                    'url' => '/admin/pages/add',
                    'link_attr' => [
                        'icon' => 'fa fa-pencil-square-o fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                    ],
                ])
            ]
        ]);
        
        $this->container->get('adminMenuLeft')->add($item);

        $item = new Menu('Users and Groups', [
            'menu_name' => 'section.usersandgroups',
            'url' => '#',
            'link_attr' => [
                'icon' => 'fa fa-users fa-fw'
            ],
            'meta_attr' => [
                'onlyDevelopersMode' => false,
                'sort' => 300
            ],
            'sub_menu' => [
                new Menu('Users', [
                    'menu_name' => 'user.list',
                    'url' => '/admin/users',
                    'link_attr' => [
                        'icon' => 'fa fa-user fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                    ],
                ]),
                new Menu('Groups', [
                    'menu_name' => 'group.list',
                    'url' => '/admin/groups',
                    'link_attr' => [
                        'icon' => 'fa fa-group fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                    ],
                ])
            ]
        ]);

        $this->container->get('adminMenuLeft')->add($item);

        $item = new Menu('System options', [
            'menu_name' => 'section.options',
            'url' => '#',
            'link_attr' => [
                'icon' => 'fa fa-gears fa-fw'
            ],
            'meta_attr' => [
                'onlyDevelopersMode' => false,
                'sort' => 400
            ],
            'sub_menu' => [
                new Menu('Options', [
                    'menu_name' => 'option.list',
                    'url' => '/admin/options',
                    'link_attr' => [
                        'icon' => 'fa fa-gear fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                    ],
                ]),
                new Menu('Option groups', [
                    'menu_name' => 'group_options.list',
                    'url' => '/admin/group_options',
                    'link_attr' => [
                        'icon' => 'fa fa-gears fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                    ],
                ])
            ]
        ]);

        $this->container->get('adminMenuLeft')->add($item);

        $item = new Menu('Developer possibility', [
            'menu_name' => 'section.only_developers',
            'url' => '#',
            'link_attr' => [
                'icon' => 'fa fa-flash fa-fw'
            ],
            'meta_attr' => [
                'onlyDevelopersMode' => true,
                'sort' => 1000
            ],
            'sub_menu' => [
                new Menu('PHP info', [
                    'menu_name' => 'developer.phpinfo',
                    'url' => '/admin/phpinfo',
                    'link_attr' => [
                        'icon' => 'fa fa-gear fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                    ],
                ]),
            ]
        ]);

        $this->container->get('adminMenuLeft')->add($item);

        if( Session::get('auth') ):
        $item = new Menu('', [
            'url' => '#',
            'link_attr' => [
                'icon' => 'fa fa-user fa-fw'
            ],
            'meta_attr' => [
                'onlyDevelopersMode' => false,
                'sort' => 100
            ],
            'sub_menu' => [
                new Menu('User edit', [
                    'menu_name' => 'user.profile',
                    'url' => '/admin/users/edit/'.Session::get('user')['id'],
                    'link_attr' => [
                        'icon' => 'fa fa-user fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                    ],
                ]),
                new Menu('Item delimiter', [
                    'menu_name' => 'user.delimiter',
                    'url' => '#',
                    'link_attr' => [
                        'icon' => 'fa fa-ban fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                        'delimiter' => true
                    ],
                ]),
                new Menu('Logout', [
                    'menu_name' => 'user.logout',
                    'url' => '/auth/logout',
                    'link_attr' => [
                        'icon' => 'fa fa-sign-out fa-fw'
                    ],
                    'meta_attr' => [
                        'onlyDevelopersMode' => false,
                    ],
                ])
            ]
        ]);
        $item1 = new Menu('', [
            'url' => '?clear_cache',
            'link_attr' => [
                'icon' => 'fa fa-refresh fa-fw'
            ],
            'meta_attr' => [
                'onlyDevelopersMode' => false,
                'sort' => 100
            ]
        ]);

        $this->container->get('adminMenuTop')->add($item);
        $this->container->get('adminMenuTop')->add($item1);
        endif;
    }
}
