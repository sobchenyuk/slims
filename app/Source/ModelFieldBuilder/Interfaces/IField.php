<?php

namespace App\Source\ModelFieldBuilder\Interfaces;

interface IField
{
	public function __construct(\stdClass $obj);
	public function noVisible($trig = true);
	public function setValue($value);
	public function __toString();
	public function correct();
}