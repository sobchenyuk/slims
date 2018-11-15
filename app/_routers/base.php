<?php
$app->get('/test1', function () {
    if (isset($_REQUEST['hauth_start']) || isset($_REQUEST['hauth_done'])) {
        Hybrid_Endpoint::process();
    }try {
        $hybridauth = new Hybrid_Auth(APP_PATH . 'config/socialAuth.php');
        $adapter = $hybridauth->authenticate("Vkontakte");
        $user_profile = $adapter->getUserProfile();
        p($user_profile);
    } catch (Exception $e) {
        echo "Ooophs, we got an error: " . $e->getMessage();
    }
})->setName('asdf');

use App\Source\Composite\Menu;

$app->get('/d', function ($t) {
    /*$admin = new Menu('AdminLeftMenu');
    $admin->add(new Menu('Home', '/'));
    $admin->add(new Menu('Sections', array(
        'data-name'=>"section",
        'data-price' => "100",
        'url' => 'test',
        "class"=>"nav",
        "id"=>"sf-344",
        "link_attr" => array(
            "class"=>"link-class active",
            "id"=>"rrer",
            "superAttr"=>"attr"
        ),
        "meta_attr" => array(
            "display" => false,
            "depth_level" => 3,
        )
    )));

    $admin->add(new Menu('Pages', '/'));
    $baseMenu = new Menu('Options', '/');
    $baseMenu->add(new Menu('Group options', '/'));
    $subMenu = new Menu('Users', '/');
    $subMenu->add(new Menu('New user', '/'));
    $subMenu->add(new Menu('All users', '/'));
    $subMenu->add(new Menu('Users permittions', '/'));
    $baseMenu->add($subMenu);
    $admin->add($baseMenu);


    $admin->getChild(8)->attributes('teste', 'test')->meta(["display"=>false, "asfd"=>"asddddd"]);
    //p($admin->getChild(3));
    //$admin->remove(7);
    //p($admin->getChild(7));
    $r = $admin->filter(function($item){
        return ( $item->meta()['display'] === false )?false:true;
    });
p($r);*/
    //p($admin);
});





$app->options('/ajax', 'App\Controllers\Admin\UniversalAjaxController:update')->add('App\Middleware\CheckAjaxMiddleware')->setName('asdf1');
/*
$app->get('/install/user_views_settings', function($req, $res, $args){
Capsule::schema()->dropIfExists('user_views_settings');
Capsule::schema()->create('user_views_settings', function($table) {
$table->increments('id');
$table->integer('user_id');
$table->string('group');
$table->string('value');
$table->string('option_type');
$table->string('code');
});
});
/*
$app->get('/install/users', function($req, $res, $args){
//DB::table('users')->get();
Capsule::schema()->dropIfExists('users');
Capsule::schema()->create('users', function($table) {
$table->increments('id');
$table->string('email');
$table->string('login');
$table->string('password');
$table->integer('active');
$table->timestamps();
});
});

$app->get('/install/groups', function($req, $res, $args){
Capsule::schema()->dropIfExists('groups');
Capsule::schema()->create('groups', function($table) {
$table->increments('id');
$table->string('name');
$table->string('description', 500);
$table->integer('active');
$table->timestamps();
});
});

$app->get('/install/options', function($req, $res, $args){
Capsule::schema()->dropIfExists('options');
Capsule::schema()->create('options', function($table) {
$table->increments('id');
$table->integer('options_group_id');
$table->string('name');
$table->string('description', 500);
$table->string('value');
$table->string('type');
$table->integer('code');
$table->timestamps();
});
});

$app->get('/install/options_group', function($req, $res, $args){
Capsule::schema()->dropIfExists('options_group');
Capsule::schema()->create('options_group', function($table) {
$table->increments('id');
$table->string('name');
$table->string('description', 500);
$table->integer('active');
$table->timestamps();
});
});

$app->get('/install/pages', function($req, $res, $args){
Capsule::schema()->dropIfExists('pages');
Capsule::schema()->create('pages', function($table) {
$table->increments('id');
$table->string('name');
$table->string('code');
$table->string('sort');
$table->string('url_prefix');
$table->integer('category_id');
$table->text('preview_text');
$table->text('detail_text');
$table->string('preview_picture');
$table->string('detail_picture');
$table->integer('show_in_menu');
$table->integer('name_for_menu');
$table->integer('active');
$table->timestamps();
});
});

$app->get('/install/sections', function($req, $res, $args){
Capsule::schema()->dropIfExists('sections');
Capsule::schema()->create('sections', function($table) {
$table->increments('id');
$table->string('name');
$table->string('code');
$table->string('sort');
$table->string('parent_id');
$table->string('path');
$table->text('detail_text');
$table->string('detail_picture');
$table->integer('show_in_menu');
$table->integer('name_for_menu');
$table->integer('active');
$table->timestamps();
});
});

*/
