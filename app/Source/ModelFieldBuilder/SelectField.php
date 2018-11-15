<?php

namespace App\Source\ModelFieldBuilder;

class SelectField extends AField
{
	protected $allowTypes = ['select', 'multiselect'];
	protected $defaultType = 'select';
	public $values = ['0'=>"Change..."];
	public $size = 5;
	public $multiple = false;
	public $default = 0;

	public function __construct(\stdClass $obj){
		if( $obj->values )
			$obj->values = array_merge($this->values, (array)$obj->values);
		
		parent::__construct($obj);
	}

	public function __toString(){
		if( !$this->visible || $this->name=='default' )
			return '';

		$str = sprintf('<select # name="%s">***</select>', $this->name);

		if( $this->multiple )
			$str = str_replace("#", "# multiple", $str);

		if( $this->multiple && $this->size)
			$str = str_replace("#", "# size=\"".$this->size."\"", $str);

		$options = '';
		foreach ((array)$this->values as $value => $name) {
			$opt = sprintf("<option value=\"%s\">%s</option>\r\n", $value, $name);

			if( $this->value !== null && (string)$this->value==(string)$value )
				$opt = str_replace("value", "selected value", $opt);
			if( $this->value === null && (string)$this->default==(string)$value )
				$opt = str_replace("value", "selected value", $opt);

			$options .= $opt;
		}

		$str = str_replace("***", $options, $str);

		return $this->toString($str);
	}
}