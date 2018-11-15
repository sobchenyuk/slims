<?php

namespace App\Source\ModelFieldBuilder;

class CheckboxField extends AField
{
	protected $allowTypes = ['checkbox'];
	protected $defaultType = 'checkbox';
	public $values = ['0'=>0, '1'=>1];

	public function __construct(\stdClass $obj){
		if( $obj->values )
			$obj->values = array_merge($this->values, (array)$obj->values);
		
		parent::__construct($obj);
	}

	public function __toString(){
		if( !$this->visible || $this->name=='default' )
			return '';

		$obj = new \stdClass();
		$obj->type = 'hidden';
		$obj->name = $this->name;
		$obj->value = $this->values[0];
		$str1 = (string) new HiddenField($obj);

		$str = sprintf('<input type="%s" name="%s" value="%s" #>', $this->type, $this->name, $this->values[1]);
		
		if( $this->value!==null && $this->value==$this->values[1] )
			$str = str_replace("#", "# checked=\"checked\"", $str);
		if( $this->value===null && $this->default!==null )
			$str = str_replace("#", "# checked=\"checked\"", $str);

		$this->className = str_replace("form-control", "", $this->className);

		return $str1.$this->toString($str);
	}
}