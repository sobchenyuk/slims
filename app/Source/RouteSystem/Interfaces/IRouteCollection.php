<?php

namespace App\Source\RouteSystem\Interfaces;

interface IRouteCollection{
	public static function add(IRouteResource $resource);
	public static function pop();
	public static function flush();
	public static function getAll();
	public static function sort($callable);
	public static function register(\Slim\App $app);
}