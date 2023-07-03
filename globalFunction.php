<?php

use Wilp\Helpers\LangReader;

if (!function_exists('__')) {
	function __($str)
	{
		return LangReader::get($str);
	}
}

if (!function_exists('dd')) {
	function dd($var)
	{
		echo '<pre>';
		print_r($var);
		echo '</pre>';
		die();
	}
}

if (!function_exists('dump')) {
	function dump($var)
	{
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}
}
