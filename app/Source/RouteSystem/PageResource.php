<?php

namespace App\Source\RouteSystem;

use \Slim\App as Slim;

/**
* 
*/
class PageResource implements Interfaces\IRouteResource
{
	protected $groupPath = '';
	protected $controller = '';
	protected $groupName = '';

	public function __construct($groupPath, $method='detail', $groupName='')
	{
		if( (strpos($groupPath, '/') !== 0) )
			$groupPath = '/'.$groupPath;
		
		if( !$method )
			$method = 'detail';

		$repl = '';
		if(preg_match("/\{(\w+)\}/", $groupPath, $m)){
			$repl = rawurlencode($m[0]);
		}

		$this->method = $method;
		//if( !$controller || !class_exists($controller) )
		$controller = '\App\Controllers\Sites\UniversalPageController';	
		$this->groupPath  = implode("/", array_map("rawurlencode", explode("/", $groupPath)));
		if($repl){
			$this->groupPath = str_replace($repl, $m[0], $this->groupPath);
		}
		$this->controller = $controller;

		$this->groupName = ($groupName)?$groupName:substr(array_pop(explode('/', $groupPath)), 0, -1);
	}

	public function getInfo(){
		return [
			'path' => $this->groupPath,
			'handle' => $this->controller,
			'name' => $this->groupName,
			'method' => $this->method,
		];
	}

	public function registerRoute(Slim $app){
		$data = $this->getInfo();
		$app->get($data['path'], $data['handle'].':'.$data['method'])->setName('page.'.$data['name']);
	}
}