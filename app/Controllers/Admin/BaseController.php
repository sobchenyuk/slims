<?php

namespace App\Controllers\Admin;

use Illuminate\Pagination\Paginator;
use App\Helpers\SessionManager as Session;
use App\Source\Factory\ModelsFactory;
use App\Source\Events\BaseContainerEvent;

class BaseController
{
    protected $controllerName = '';
    protected $containerSlim;
    protected $resourse = true;
    public $pageOrderBy = 'id';
    public $pageOrderType = 'asc';


    protected $data = array(
        'title' => '',
        'description' => '',
        'keywords' => '',
        'h1' => '',
        'flash' => array(),
        'page_counts' => [5, 10, 15, 25, 50, 100, 150],
    );

    public function __construct($container)
    {
        $this->containerSlim = $container;

        $this->view = $container->get('view');
        $this->csrf = $container->get('csrf');
        $this->flash = $container->get('flash');
        $this->router = $container->get('router');

        if ($messages = $this->containerSlim->get('flashMess')) {
            foreach ($messages as $key => $value) {
                $this->data[$key] = $value[0];
            }
        }

        $this->init();
    }

    protected function init()
    {
        if (!isset($this->controllerName) || !$this->controllerName)
            return;

        $arDataContr = array('title', 'description', 'keywords', 'h1');

        foreach ($arDataContr as $name) {
            $this->data[$name] = $this->controllerName;
        }
        $this->data['all_e_link'] = 'list.' . $this->controllerName;
        if ($this->resourse) {
            $this->data['create_link'] = 'add.' . $this->controllerName;
            $this->data['edit_link'] = 'edit.' . $this->controllerName;
            $this->data['store_link'] = 'store.' . $this->controllerName;
            $this->data['save_link'] = 'save.' . $this->controllerName;
            $this->data['delete_link'] = 'delete.' . $this->controllerName;
        }
        $this->data['system_options'] = $this->containerSlim->systemOptions;

        $this->data['menu_left'] = $this->containerSlim->get('adminMenuLeft');
        $this->data['menu_top'] = $this->containerSlim->get('adminMenuTop');
    }

    protected function initRoute($req, $res)
    {
        $this->request = $req;
        $this->response = $res;

        $s = $req->getAttribute('route')->getName();
        $this->data['current_route_name'] = $s;

        $this->containerSlim->get('logger')->addInfo("Run admin page: ", [Session::get('user')['login']]);
        $this->containerSlim->get('logger')->addInfo("Get route: ", [$s]);

        $model = ModelsFactory::getModel('UserViewsSettings');
        $result = $model->where('user_id', Session::get('user')['id'])->where('group', 'last.page.' . basename($req->getUri()->getPath()))->where('code', 'page')->first();

        if ($result)
            $current_page = $result->value;

        Paginator::currentPageResolver(function () use ($current_page) {
            return $current_page;
        });

        $result = $model->where('user_id', Session::get('user')['id'])->where('group', 'items.perpage.' . basename($req->getUri()->getPath()))->where('code', 'count_page')->first();

        if ($result){
            $this->pagecount = $result->value;
            $this->data['page_count'] = $this->pagecount;
        }

        $result = $model->where('user_id', Session::get('user')['id'])->where('group', 'order.type.' . basename($req->getUri()->getPath()))->where('code', 'order_by')->first();

        $this->pageOrderBy = "id";
        if ($result){
            $this->pageOrderBy = $result->value;
        }
        $this->data['page_order_by'] = $this->pageOrderBy;

        $result = $model->where('user_id', Session::get('user')['id'])->where('group', 'order.type.' . basename($req->getUri()->getPath()))->where('code', 'order_type')->first();

        $this->pageOrderType = "asc";
        if ($result){
            $this->pageOrderType = $result->value;
        }
        $this->data['page_order_type'] = $this->pageOrderType;

        if (!$this->controllerName)
            $this->controllerName = substr($s, strpos($s, '.') + 1);

        $this->init();
        $this->csrf($req);
    }

    protected function csrf($req)
    {
        $this->data['csrf'] = new \stdClass();
        $this->data['csrf']->nameKey = $this->csrf->getTokenNameKey();
        $this->data['csrf']->valueKey = $this->csrf->getTokenValueKey();
        $this->data['csrf']->name = $req->getAttribute('csrf_name');
        $this->data['csrf']->value = $req->getAttribute('csrf_value');
    }

    protected function getFields(array $arFields, $arSave = array(), $arHide = array())
    {
        $arHide = array_merge($arHide, array('id', 'created_at', 'updated_at'));

        return array_diff(
            $arFields,
            array_diff($arHide, $arSave)
        );
    }

    protected function render($templateName)
    {
        $this->beforeRender();

        $res = $this->view->render($this->response, $templateName, $this->data);

        $this->afterRender();

        return $res;
    }

    protected function beforeRender()
    {
        $event = new BaseContainerEvent($this->containerSlim, $this->data);
        $event = $this->containerSlim->dispatcher->dispatch('basecontroller.render.before', $event);
        $this->data = $event->getParams();
    }

    protected function afterRender()
    {

    }
}