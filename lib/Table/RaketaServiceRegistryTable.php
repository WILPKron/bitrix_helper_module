<?php

namespace Raketa\Plastfoil\Table;

use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Raketa\Plastfoil\ORM\Fields\Validators\CodeValidator;
use Bitrix\Main\ORM\Fields\Validators;
class RaketaServiceRegistryTable extends RaketaDataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'r_service_registry';
	}
	public static function getMap()
	{
		return [
			new IntegerField('ID', ['primary' => true, 'autocomplete' => true, 'title' => 'ID']),
			new IntegerField('UF_INDEX'),
			new TextField('UF_NAME', [ 'title' => 'Имя компании' ]),
			new TextField('UF_OGRN', [ 'title' => 'ОГРН' ]),
			new DateField('UF_DATE_REGISTRATION', [ 'title' => 'Дата регистрации юр. лица', "nullable" => true ]),
			new TextField('UF_EXTERNAL_ID', [ 'title' => 'Внешний ключ' ]),
			new DateField('UF_DATE_ISSUE', [ 'title' => 'Дата выдачи', "nullable" => true ]),
			new DateField('UF_DATE_RENEWAL', [ 'title' => 'Дата продления', "nullable" => true ]),
			new TextField('UF_CITY', [ 'title' => 'Регион активности' ]),
			new TextField('UF_RESPONSIBLE_EMPLOYEE', [ 'title' => 'Ответственный сотрудник' ]),
			new TextField('UF_AUTHORIZATION_TYPE', [ 'title' => 'Тип авторизации' ]),
			new TextField('UF_CONSTRUCTIVE', [ 'title' => 'Конструктив' ]),
			new TextField('UF_INFO', [ 'title' => 'ФИО сотрудников, прошедших обучение' ]),
			new TextField('UF_COUNTRY', [ 'title' => 'Страна' ]),
			new TextField('UF_FZ', [ 'title' => 'Федиральный округ' ]),
			new TextField('UF_CODE', [ 'title' => 'Символьный код', 'validation' => [__CLASS__, 'validateCode'], ]),
		];
	}
	public static function validateCode()
	{
		return [
			new Validators\UniqueValidator('поле "#FIELD_TITLE#" должно быть уникальным'),
		];
	}
}
