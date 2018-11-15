<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model as ModelEloquent;
use \Illuminate\Database\Capsule\Manager as DB;

class BaseModel extends ModelEloquent
{
	protected $allFields;

	public function getColumnsNames(array $arAdditionalField=[])
	{
		if( $this->allFields )
			return $this->allFields;

/*	    $connection = DB::connection();
	    $connection->getSchemaBuilder();

	    $results = $connection->select('PRAGMA table_info('.$this->table.')');
	    $results = $connection->getPostProcessor()->processColumnListing($results);
*/
	    $results = DB::connection()->getSchemaBuilder()->getColumnListing($this->table);
	    $this->allFields = array_merge($results, $arAdditionalField);
	    return $this->allFields;
	}

	public function getAnnotations()
	{
		$file = RESOURCE_PATH.'models_field_info/'.$this->get_class_name().'.json';
	    
	    if( !is_file($file) )
	    	return false;

	    $fjson = file_get_contents($file);

	    return json_decode($fjson);
	}

	protected function get_class_name(){
		
		$classname = strtolower(get_class($this));

		if ($pos = strrpos($classname, '\\')) 
			return substr($classname, $pos + 1);
    	
    	return $pos;
	}
} 