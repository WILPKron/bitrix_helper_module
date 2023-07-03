<?php

namespace Wilp\Helpers;

use Bitrix\Main\Context;

class LangReader
{
	public static string $pathToLangDir = __DIR__ . '../../../../../../lang/';

	public static function get($str = '')
	{
		if (empty($str)) {
			return $str;
		}

		$lang = self::getSiteLang();
		$pathArray = explode('.', $str);

		$path = self::$pathToLangDir . "$lang";

		foreach ($pathArray as $key => $item) {
			$tempPath = $path . '/' . $item;
			$filePath = $tempPath . '.php';
			if (is_file($filePath) || is_dir($tempPath)) {
				unset($pathArray[$key]);
				$path = is_file($filePath) ? $filePath : $tempPath;
			}
		}

		if (!is_file($path)) {
			return '';
		}

		$fileData = require $path;

		foreach ($pathArray as $item) {
			if (empty($fileData[$item])) {
				return $str;
			}
			$fileData = $fileData[$item];
		}

		return $fileData;
	}

	public static function getSiteLang()
	{
		$context = Context::getCurrent();
		$language = $context->getLanguage() ?? 'ru';

		return $language;
	}
}
