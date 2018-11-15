<?php

namespace App\Source\RouteSystem\Interfaces;

interface IRouteResource{
	public function __construct($groupPath, $controller, $groupName='');
	public function getInfo();
	public function registerRoute(\Slim\App $app);
}