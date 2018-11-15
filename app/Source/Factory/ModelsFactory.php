<?php

namespace App\Source\Factory;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Illuminate\Support\Str;

/**
* 
*/
class ModelsFactory
{
	
	protected static $namespaceModel = '\\App\\Models\\';

	function __construct(){}

	public static function getModel($modelName, $data = []){
		$modelName = self::$namespaceModel.Str::ucfirst($modelName);

		if( class_exists($modelName) )
			return ( !empty($data) ) ? new $modelName($data): new $modelName();

		return new \stdClass();
	}

	public static function getModelWithRequest(Request $req, $data = []){
		$className = self::getClassName($req->getAttribute('route')->getName());

		if( class_exists($className) )
			return ( !empty($data) ) ? new $className($data): new $className();

		return new \stdClass();
	}

	protected static function getClassName($routeName){
		$className = Str::ucfirst(substr($routeName, strpos($routeName, '.')+1));
		if( preg_match_all("/_\w/s", $className, $m) ){
			foreach ($m[0] as $v) {
				$className = str_replace($v, strtoupper($v[1]), $className);
			}
		}

		return self::$namespaceModel.$className.'s';
	}
}