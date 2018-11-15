<?php

namespace Modules\Core\Source\Libs\Options;

use Illuminate\Database\Eloquent\Collection;

class OptionsFacade
{
	protected $options;
	protected $arOptions = [];

	public function __construct(Collection $options){
		$this->options = $options;

		foreach ($options as $value) {
			$this->arOptions[$value->code] = $value;
		}
	}

	public function get($name){
		return $this->arOptions[$name];
	}

	public function getValue($name){
		return $this->arOptions[$name]->value;
	}

	public function isDevMode(){
		return (bool)$this->getValue('develop_mode');
	}

	public function isFrozenMode(){
		return (bool)$this->getValue('freeze_mode');
	}

	public function isHideFunctionality($value=true){
		return (($this->isFrozenMode() || !$this->isDevMode()) && $value);
	}
}