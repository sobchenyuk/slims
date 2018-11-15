<?php

namespace App\Models;

class UserViewsSettings extends BaseModel
{
	protected $table = 'user_views_settings';

	protected $fillable = ['id', 'user_id', 'group', 'option_type', 'code', 'value'];

	public $timestamps = false;
}