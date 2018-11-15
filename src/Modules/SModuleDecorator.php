<?php

namespace SlimCMS\Modules;

use App\Contracts\Modules\IModuleDecorator;
use App\Contracts\Modules\AModule;
use App\Contracts\Modules\IModule;

/**
* 
*/
class SModuleDecorator extends AModule implements IModuleDecorator
{
	protected $module;
	
	public function __construct(IModule $module)
	{
		$this->module = $module;
	}

	public function __get($name){
		return $this->module->$name;
	}

	public function __call($name, $args){
		$l = count($args);

		switch ($l) {
			case 1:
				return $this->module->$name($args[0]);
			case 2:
				return $this->module->$name($args[0], $args[1]);
			case 3:
				return $this->module->$name($args[0], $args[1], $args[2]);
			
			default:
				return $this->module->$name();
		}
	}
}