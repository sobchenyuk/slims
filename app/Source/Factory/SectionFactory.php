<?php

namespace App\Source\Factory;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Illuminate\Support\Str;
use App\Models\Sections;
use App\Source\Factory\ModelsFactory;

/**
* 
*/
class SectionFactory
{
	function __construct(){}

	public static function getSectionWithRequest(Request $req){
		$sectionId = self::getSectionId($req->getAttribute('route')->getName());
		
		if( $sectionId > 0 )
			return ModelsFactory::getModel('sections')->find($sectionId);

		return new \stdClass();
	}

	protected static function getSectionId($routeName){
		return (int)substr($routeName, strpos($routeName, '.')+2);
	}
}