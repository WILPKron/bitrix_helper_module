<?php

namespace Wilp\Helpers;

use Bitrix\Main\Context;

class LangReader
{
	/** @var string $pathToLangDir перемення корневого каталога языковых версий **/
	public static string $pathToLangDir = __DIR__ . '../../../../../../local/lang/';

	/**
	 * Метод для получения языковых данных в зависимости от языковой версии сайт. Корень языковых данных считается от
	 * переменной $pathToLangDir, поиск начинается с этой директории.
	 * @param string $str строка пути до нужной переменной. Язык для директории выбирается автоматически
	 * если не указана переменная $locale.
	 * @param array $replace массив ключ => значение для замены меток ":" на данные указанные переменны регистр
	 * метки учитывается
	 * Пример: ( :МЕТКА - все заглывные буквы, :Метка - будет начинаться с заглавной буквы )
	 * @param string $locale переменная для установка локали выбираемых данных
	 * @return mixed возвращает данные в соответсвие с ходными параметрами и языковой версией сайта
	**/
	public static function get($str, $replace = [], $locale = null)
	{
		$lang = $locale ?? self::getSiteLang();
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
			return $lang. '.' . $str;
		}

		$fileData = require $path;

		foreach ($pathArray as $item) {
			if (empty($fileData[$item])) {
				return $lang. '.' . $str;
			}
			$fileData = $fileData[$item];
		}

		if (count($replace) > 0) {
			foreach ($replace as $key => $word) {
				$fileData = self::replace_string($fileData, $key, $word);
			}
		}

		return $fileData;
	}

	/**
	 * Метод для замены меток в данных как строк так и массивов
	 * @param mixed $object данные для замены может быть как массивом так и строкой
	 * @param string $key метка
	 * @param string $word на что заменить метку
	**/

	public static function replace_string($object, $key = '', $word = '')
	{
		if (is_string($object)) {
			$pattern = '/:'. $key .'/i';
			preg_match_all($pattern, $object, $matches, PREG_SET_ORDER, 0);
			foreach ($matches as $match) {
				$temp = match ($match[0]) {
					':' . mb_strtoupper($key) => mb_strtoupper($word),
					':' . mb_ucfirst($key) => mb_ucfirst($word),
					default => $word
				};
				$object = str_replace($match[0], $temp, $object);
			}
		} else {
			foreach ($object as &$item) {
				$item = self::replace_string($item, $key, $word);
			}
		}
		return $object;
	}

	public static function getSiteLang()
	{
		$context = Context::getCurrent();
		$language = $context->getLanguage() ?? 'ru';

		return $language;
	}
}
