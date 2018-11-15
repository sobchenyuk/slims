<?php

namespace App\Source\ModelFieldBuilder;

class Field implements Interfaces\IField
{
	protected $correct = false;
	protected $inputObject;
	protected $allowTypes = ['text', 'checkbox', 'hidden', 'select'];
	protected $defaultType = 'text';

	public $visible = true;
	public $sort = 100;
	public $placeholder;
	public $value;
	public $className;
	public $idName;

	public function __construct(\stdClass $obj){
		$this->checkCorrectedFormat($obj);
		if( !$this->correct() )
			return;

		$this->setInput($obj);
	}

	public function correct(){
		return $this->correct;
	}

	protected function checkCorrectedFormat($obj){
		if( isset($obj->name) && $obj->name )
			$this->correct = true;
	}

	protected function setInput($obj){
		$this->inputObject = $obj;
		
		foreach ($obj as $key => $value) {
			$this->$key = $value;
		}

		$this->type = (in_array($obj->type, $this->allowTypes))?$obj->type:$this->defaultType;
	}

	public function noVisible($trig = true){
		$this->visible = (!$trig && $this->name != 'default');
	}

	public function setValue($value){
		$this->value = $value;
	}

	public function __toString(){
		if( !$this->visible || $this->name=='default' )
			return '';

		$str = sprintf('<input type="%s" name="%s" #>', $this->type, $this->name);
		
		if( $this->value!==null )
			$str = str_replace("#", "# value=\"".$this->value."\"", $str);

		if($this->className)
			$str = str_replace("#", "# class=\"".$this->className."\"", $str);
		
		if($this->idName)
			$str = str_replace("#", "# id=\"".$this->idName."\"", $str);

		if($this->placeholder)
			$str = str_replace("#", "# placeholder=\"".$this->placeholder."\"", $str);
		
		return str_replace("#", "", $str);
	}
}