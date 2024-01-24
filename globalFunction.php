<?php

use Wilp\Helpers\LangReader;

if (!function_exists('__')) {
	function __($str = null, $replace = [], $locale = null)
	{
		return LangReader::get($str, $replace, $locale);
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

if (!function_exists('mb_ucfirst')) {
	function mb_ucfirst($string, $encoding = "utf8")
	{
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, null, $encoding);
		return mb_strtoupper($firstChar, $encoding) . mb_strtolower($then, $encoding);
	}
}

if(!function_exists('transliterate')) {
	function transliterate($sTextCyr = null, $sTextLat = null)
	{
		$aCyr = array(
			'э', 'ы', 'ж', 'ч', 'щ', 'ш', 'ю', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я', ' ',
			'Э', 'Ы', 'Ж', 'Ч', 'Щ', 'Ш', 'Ю', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я', ' ');

		$aLat = array(
			'e', 'y', 'zh', 'ch', 'sch', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', '', '', 'ya', '-',
			'E', 'Y', 'Zh', 'Ch', 'Sch', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', '', '', 'Ya', '-');

		if ($sTextCyr) {
			return str_replace($aCyr, $aLat, $sTextCyr);
		} elseif ($sTextLat) {
			return str_replace($aLat, $aCyr, $sTextLat);
		}

		return null;
	}
}

if(!function_exists('str_ends_with')) {
	function str_ends_with(string $haystack, string $needle): bool
	{
		return mb_substr($haystack, mb_strlen($haystack) - mb_strlen($needle)) == $needle;
	}

}
