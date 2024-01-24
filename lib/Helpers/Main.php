<?php

namespace Wilp\Helpers;

class Main
{
	public static function mainPage()
	{
		return \CSite::InDir('/index.php') ||
			\CSite::InDir('/en/index.php');
	}

	public static function printSprite(string $id, int $w = 14, int $h = 14, string $class = '', string $dataAttr = ''): string
	{
		$link = SITE_TEMPLATE_PATH . '/frontend/assets/img/sprite.svg';
		$svg = '<svg ';
		if(!empty($class)) $svg .= 'class="'. $class . '"';

		if($w !== 0 && $h !== 0) {
			$svg .= ' width="' . $w . '"';
			$svg .= ' height="' . $h . '"';
		}

		if(!empty($dataAttr)) $svg .= '" '. $dataAttr;

		$svg .= '><use xlink:href="' . $link . '#' . $id . '"></use></svg>';

		return $svg;
		//return '<svg ' . ($class ? 'class="' . $class . '"' : '') . ' width="' . $w . '" height="' . $h . '" '. $dataAttr .'><use xlink:href="' . $link . '#' . $id . '"></use></svg>';
	}

	public static function pre($obj)
	{
		echo '<pre>';
			print_r($obj);
		echo '</pre>';
	}
	public static function var_dump($obj)
	{
		echo '<pre>';
			var_dump($obj);
		echo '</pre>';
	}

	public static function initOneBlockElements(array $elements = []): void
	{
		foreach ($elements as $element) {
			new $element;
		}
	}

	public static function getApp()
	{
		global $APPLICATION;
		return $APPLICATION;
	}

	public static function convertBytes($size)
	{
		$i = 0;
		while (floor($size / 1024) > 0) {
			++$i;
			$size /= 1024;
		}

		$size = str_replace('.', ',', round($size, 1));
		switch ($i) {
			case 0: return $size .= ' байт';
			case 1: return $size .= ' КБ';
			case 2: return $size .= ' МБ';
		}
	}
}
