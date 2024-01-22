<?php
namespace Raketa\Plastfoil\ORM\Fields\Validators;

use Bitrix\Main\ORM\Fields\Validators\Validator;
use Bitrix\Main\ORM;

class CodeUnique extends Validator
{
	public function validate($value, $primary, array $row, ORM\Fields\Field $field)
	{
		$element = $field->getEntity()->getDataClass()::getList([
			'filter' => [
				$field->getName() => $value
			]
		])->fetch();
		if(!empty($element)) {
			return $this->getErrorMessage($value, $field);
		}
		return true;
	}
}
