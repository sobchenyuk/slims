<?php

namespace App\Models;

class Pages extends BaseModel
{
	protected $table = 'pages';

	protected $fillable = array (
  0 => 'name',
  1 => 'code',
  2 => 'url_prefix',
  3 => 'preview_text',
  4 => 'detail_text',
  5 => 'preview_picture',
  6 => 'detail_picture',
  7 => 'show_in_menu',
  8 => 'name_for_menu',
  9 => 'active',
  10 => 'slogan',
  11 => 'fullname',
  12 => 'sort',
  13 => 'category_id',
);
}