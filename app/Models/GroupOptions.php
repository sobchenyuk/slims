<?php

namespace App\Models;

class GroupOptions extends BaseModel
{
	protected $table = 'options_group';

	protected $fillable = ['name', 'description', 'active'];

	public function getOptions()
    {
        return $this->belongsTo('App\Models\Options', 'id', 'options_group_id')->get()->toArray();
    }
}