<?php

namespace App\Source\ModelFieldBuilder;

abstract class AField implements Interfaces\IField
{
	protected $correct = false;
	protected $inputObject;
	protected $allowTypes = ['string', 'checkbox', 'hidden', 'select'];
	protected $defaultType = 'string';

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

	public function getInputParams(){
		return $this->inputObject;
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

		if($this->name == 'default')
			$this->visible = false;
	}

	public function noVisible($trig = true){
		$this->visible = (!$trig && $this->name != 'default');
	}

	public function setValue($value){
		$this->value = $value;
	}

	protected function toString($str){
		if($this->className)
			$str = str_replace("#", "# class=\"".$this->className."\"", $str);

		if($this->idName)
			$str = str_replace("#", "# id=\"".$this->idName."\"", $str);

		if( $this->no_show_value )
			$str = preg_replace("/value=\"(.*)\"/s", "", $str);

		return str_replace("#", "", $str);
	}
}