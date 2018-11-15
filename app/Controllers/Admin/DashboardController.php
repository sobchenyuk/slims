<?php

namespace App\Controllers\Admin;

use App\Source\Factory\ModelsFactory;

class DashboardController extends BaseController
{
	public function index($req, $res){
		$this->controllerName = $req->getAttribute('route')->getName();
		$this->resourse = false;
		$this->initRoute($req, $res);
		$this->data['h1'] = 'Dashboard';

		$this->data['cnt'] = new \stdClass();
		$this->data['cnt']->pages = ModelsFactory::getModel('pages')->count();
		$this->data['cnt']->users = ModelsFactory::getModel('users')->count();
		$this->data['cnt']->options = ModelsFactory::getModel('options')->count();
		if( $this->containerSlim->get('db')->schema()->hasTable('sections') )
			$this->data['cnt']->sections = ModelsFactory::getModel('sections')->count();

		$this->view->render($res, 'admin\dashboard.twig', $this->data);
	}
}
