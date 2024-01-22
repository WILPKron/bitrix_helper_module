<?php

namespace Raketa\Plastfoil\Helpers;

use \Bitrix\Main\Application;

class Form
{
	/**
	 * Функция для проверки обязательных полей формы
	 * @param array $requiredField ключи полей обязательных для заполнения
	 * @param array $requestField поля со значениями которые необходимо проверить
	 * @return array список вида ключ => сообщение с обязательными полями
	**/
	public static function requiredField($requiredField, $requestField)
	{
		$errorField = [];
		foreach ($requiredField as $fieldKey) {
			if (empty($requestField[$fieldKey])) {
				$errorField[$fieldKey] = 'Поле обязательно для заполнения';
			}
		}
		return $errorField;
	}
}
