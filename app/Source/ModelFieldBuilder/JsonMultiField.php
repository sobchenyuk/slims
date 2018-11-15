<?php

namespace App\Source\ModelFieldBuilder;

class JsonMultiField extends AField
{
	protected $allowTypes = ['jsonMulti'];
	protected $defaultType = 'jsonMulti';

	public function __construct(\stdClass $obj){
		parent::__construct($obj);
	}

	public function __toString(){
		$str  = '<div class="json-multiply json-multi-'.$this->name.'"><div class="form-group input-group">';
		$str .= '<a href="javascript:void(0);" class="input-group-addon add btn btn-primary">+</a>';
		$str .= sprintf('<input disabled type="%s" name="%s" value="%s" #>', $this->type, $this->name, htmlspecialchars($this->value));
		$str .= '</div></div>';
		
		$this->className = "disabled-jsonmulti jsonmultivalue ".$this->className;

		return $this->toString($str).$valueField;
	}
}