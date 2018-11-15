<?php

namespace App\Models;

class Options extends BaseModel
{
	protected $table = 'options';

	protected $fillable = ['options_group_id', 'name', 'description', 'value', 'values', 'type',  'code', 'frozen'];

	public function getGroupOptions()
    {
        return $this->hasOne('App\Models\GroupOptions', 'id', 'options_group_id')->get()[0];
    }
}