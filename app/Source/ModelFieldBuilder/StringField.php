<?php

namespace App\Source\ModelFieldBuilder;

class StringField extends AField
{
	protected $allowTypes = ['string', 'email', 'password'];
	protected $defaultType = 'string';

	public function __construct(\stdClass $obj){
		parent::__construct($obj);
	}

	public function __toString(){
		if( !$this->visible || $this->name=='default' )
			return '';

		$str = sprintf('<input type="%s" name="%s" #>', $this->type, $this->name);
		
		if( $this->value!==null )
			$str = str_replace("#", "# value=\"".$this->value."\"", $str);

		if( $this->value===null && $this->default!==null )
			$str = str_replace("#", "# value=\"".$this->default."\"", $str);

		if($this->placeholder)
			$str = str_replace("#", "# placeholder=\"".$this->placeholder."\"", $str);
		
		return $this->toString($str);
	}
}