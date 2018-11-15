<?php

namespace App\Controllers\Sites;

use App\Models\Options;
use App\Models\Pages;
use App\Source\Events\BaseContainerEvent;

/**
* 
*/
class BaseController
{
	protected $c;

	protected $data = array(
			'title' => '',
			'description' => '',
			'keywords' => '',
			'h1' => '',
		);

	protected $request;
	protected $result;

	public function __construct($container){
		$this->view = $container->get('view');
		$this->csrf = $container->get('csrf');
		$this->flash = $container->get('flash');
		$this->router = $container->get('router');
		
		$this->c = $container;

		$this->addDataForView();
	}

	protected function csrf(){
		$this->data['csrf'] = new \stdClass();
    	$this->data['csrf']->nameKey = $this->csrf->getTokenNameKey();
    	$this->data['csrf']->valueKey = $this->csrf->getTokenValueKey();
    	$this->data['csrf']->name = $this->request->getAttribute('csrf_name');
    	$this->data['csrf']->value = $this->request->getAttribute('csrf_value');
	}

	public function addDataForView(){
		$this->data['options'] = Options::where('options_group_id', 2)->get()->toArray();
		$options = [];
		while($option = array_shift($this->data['options'])){
			$options[$option['code']] = $option;
		}
		$this->data['options'] = $options;

		$this->menu = Pages::where('show_in_menu', 1)->where('active', 1)->orderBy('sort', 'asc')->get()->toArray();
		$this->data['pageData'] = new \stdClass();
	}

	protected function setRequestResult($req, $res){
		$this->request = $req;
		$this->result  = $res;
	}

	protected function menuCreator(){
		$obj = new \stdClass();

		$obj->request = $this->request;
		$obj->response = $this->result;
		$obj->menu = [];

		$event = new BaseContainerEvent($this->c, $obj);
        $event = $this->c->dispatcher->dispatch('publiccontroller.menu.logic', $event);

		$this->data['menu'] = $event->getParams()->menu;
	}

	protected function setMetaData(){
		$this->data['title'] = $this->data['pageData']->name.' page';
		$this->data['description'] = $this->data['pageData']->preview_text;
		$this->data['keywords'] = '';
		$this->data['h1'] = $this->data['pageData']->name;

		if( $id = $this->data['pageData']->category_id ){
			$this->data['categoryData'] = Pages::find($id);
		}
	}

	protected function beforeRender(){

		$this->menuCreator();
		
		$this->setMetaData();
		
		$this->csrf();
	}

	protected function afterRender(){

	}

	public function render($template){
		$this->beforeRender();

		$obj = new \stdClass();
		$obj->request = $this->request;
		$obj->response = $this->result;
		$obj->pageData = $this->data;

		$event = new BaseContainerEvent($this->c, $obj);
		$event = $this->c->dispatcher->dispatch('publiccontroller.render.before', $event);
		$this->data = $event->getParams()->pageData;

		/*$store = $this->c->cache->store();;
		$cacheName = 'controller.universal.before.render'.md5($this->request->getUri());
		if( !$store->has($cacheName) ){
			$this->beforeRender();

			$obj = new \stdClass();
			$obj->request = $this->request;
			$obj->response = $this->result;
			$obj->pageData = $this->data;

			$event = new BaseContainerEvent($this->c, $obj);
			$event = $this->c->dispatcher->dispatch('publiccontroller.render.before', $event);
			$this->data = $event->getParams()->pageData;

			$store->put($cacheName, $this->data, 60);
		} else {
			$this->data = $store->get($cacheName);
		}*/

		$this->view->render($this->result, $template, $this->data);

		$this->afterRender();

		$obj = new \stdClass();
		$obj->request = $this->request;
		$obj->response = $this->result;
		$obj->pageData = $this->data;
		$event = new BaseContainerEvent($this->c, $obj);
		$this->c->dispatcher->dispatch('publiccontroller.render.after', $event);

		return $this->result;
	}
}