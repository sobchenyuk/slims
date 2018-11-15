<?php

namespace App\Source\Factory;

use App\Source\ModelFieldBuilder\Interfaces\IFieldFactory;
use App\Source\ModelFieldBuilder\HiddenField;
use App\Source\ModelFieldBuilder\CheckboxField;
use App\Source\ModelFieldBuilder\RadioField;
use App\Source\ModelFieldBuilder\JsonMultiField;
use App\Source\ModelFieldBuilder\SelectField;
use App\Source\ModelFieldBuilder\TextField;
use App\Source\ModelFieldBuilder\StringField;
use App\Source\ModelFieldBuilder\UploadFile;


class FieldFactory implements IFieldFactory
{
	public static function getField(\stdClass $obj){
		switch ($obj->type) {
			case 'file':
				return new UploadFile($obj);
			case 'hidden':
				return new HiddenField($obj);
			case 'checkbox':
				return new CheckboxField($obj);
			case 'radio':
				return new RadioField($obj);
			case 'jsonMulti':
				return new JsonMultiField($obj);
			case 'select':
			case 'multiselect':
				return new SelectField($obj);
			case 'text':
			case 'html':
				return new TextField($obj);
			default:
				return new StringField($obj);
		}
	}
}