<?php

namespace App\Source\ModelFieldBuilder\Interfaces;

interface IFieldFactory
{
	public static function getField(\stdClass $obj);
}