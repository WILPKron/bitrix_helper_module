<?php

namespace Wilp\Helpers;

use \Bitrix\Main\Application;

class Form
{
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
