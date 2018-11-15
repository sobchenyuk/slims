<?php

function p($data, $die=false){
	if (function_exists("dump")) {
		dump($data);
		echo '<style>pre.sf-dump{font-size:16px;}</style>';
	} else {
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}

	if( $die ) die;
}

function memoryFormat($m){
	if ($m < 1024)
    	$m = $m." b";
	elseif ($m < 1048576)
	    $m = round($m/1024,2)." kb";
	else
	    $m = round($m/1048576,2)." mb";

	return $m;
}

function parse_classname ($name)
{
	return array(
		'namespace' => array_slice(explode('\\', $name), 0, -1),
		'classname' => join('', array_slice(explode('\\', $name), -1)),
	);
}