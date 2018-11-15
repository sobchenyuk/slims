<?php

namespace App\Source\ModelFieldBuilder;

class TextField extends AField
{
	protected $allowTypes = ['text', 'html'];
	protected $defaultType = 'text';

	public $row = 15;
	public $cols = 15;

	public function __construct(\stdClass $obj){
		parent::__construct($obj);
	}

	public function __toString(){
		if( !$this->visible || $this->name=='default' )
			return '';

		$str = sprintf('<textarea name="%s" #>***</textarea>', $this->name);

		if( $this->value!==null )
			$str = str_replace("***", $this->value, $str);

		if( $this->value===null && $this->default!==null )
			$str = str_replace("***", $this->default, $str);
		else
			$str = str_replace("***", "", $str);

		if($this->placeholder)
			$str = str_replace("#", "# placeholder=\"".$this->placeholder."\"", $str);

		if($this->rows)
			$str = str_replace("#", "# rows=\"".$this->rows."\"", $str);
		if($this->cols)
			$str = str_replace("#", "# cols=\"".$this->rows."\"", $str);

		$this->className = $this->className." ".$this->type."-".$this->name;
		if( $this->type=='html' )
			$this->className = $this->className." tinymce";

		return $this->toString($str);
	}
}