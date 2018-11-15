<?php

namespace App\Models;

class Groups extends BaseModel
{
	protected $table = 'groups';

	protected $fillable = ['name', 'description', 'active'];
}