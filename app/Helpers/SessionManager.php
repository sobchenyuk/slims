<?php

namespace App\Helpers;

class SessionManager
{
	public static function put($name, $value){
		$_SESSION[$name] = $value;
	}

	public static function push($name, $value){
		$arVars = explode('.', $name);

		if( !is_array($_SESSION[$arVars[0]]) )
			$_SESSION[$arVars[0]] = array();

		$_SESSION[$arVars[0]][$arVars[1]] = $value;
	}

	public static function get($name, $default=''){
		if( strpos($name, '.') ){
			$arVars = explode('.', $name);

			return ( self::has($arVars[0]) ) ? $_SESSION[$arVars[0]][$arVars[1]] : $default ;
		} else {
			return ( self::has($name) ) ? $_SESSION[$name] : $default ;
		}
	}

	public static function pull($name, $default=''){
		$res = self::get($name, $default);
		self::forget($name);

		return $res;
	}

	public static function forget($name){
		unset($_SESSION[$name]);
	}

	public static function has($name){
		return isset($_SESSION[$name]);
	} 

	public static function flush(){
		$_SESSION = array();
	}
}