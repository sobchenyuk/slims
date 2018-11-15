<?php

namespace App\Source\RouteSystem;

use \Slim\App as Slim;

/**
* 
*/
class AdminResource implements Interfaces\IRouteResource
{
	protected $groupPath = '';
	protected $controller = '';
	protected $groupName = '';

	public function __construct($groupPath, $controller='\App\Controllers\Admin\UniversalController', $groupName='')
	{
		if( (strpos($groupPath, '/') !== 0) )
			$groupPath = '/'.$groupPath;
		
		if( !class_exists($controller) )
			throw new \InvalidArgumentException("I don't find controller: ".$controller);
		
		$this->groupPath  = $groupPath;
		$this->controller = $controller;

		$a = explode('/', $groupPath);
		$this->groupName = ($groupName)?$groupName:substr(array_pop($a), 0, -1);
	}

	public function getInfo(){
		return [
			'path' => $this->groupPath,
			'handle' => $this->controller,
			'name' => $this->groupName
		];
	}

	public function registerRoute(Slim $app){
		$data = $this->getInfo();

		$app->group($data['path'], function () use ($data) {
	    	$this->get('', $data['handle'].':index')->setName('list.'.$data['name']);
	    	$this->get('/add', $data['handle'].':add')->setName('add.'.$data['name']);
	    	$this->get('/edit/{id:\d+}', $data['handle'].':edit')->setName('edit.'.$data['name']);
	    	$this->map(['PUT', 'POST'], '/add', $data['handle'].':doAdd')->setName('store.'.$data['name']);
	    	$this->map(['PUT', 'POST'], '/edit', $data['handle'].':doEdit')->setName('save.'.$data['name']);
	    	$this->map(['DELETE', 'POST'], '/delete/{id:\d+}', $data['handle'].':doDelete')->setName('delete.'.$data['name']);
	    });
	}
}