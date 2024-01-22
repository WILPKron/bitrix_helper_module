<?php

namespace Raketa\Plastfoil\Table\Menu;

use Bitrix\Main\Entity\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Raketa\Plastfoil\Table\RaketaDataManager;

class MenuEntityTable extends RaketaDataManager
{
	public static function getTableName()
	{
		return 'r_menu_entity';
	}
	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => 'ID'
				]
			),
			new BooleanField('ACTIVE', [
				'values' => array('N', 'Y'),
				'default' => 'Y',
				'title' => 'Активное',
			]),
			new TextField('NAME', [
				'title' => 'Название',
				'required' => true,
			]),
			new TextField('CODE', [
				'title' => 'Символьный код',
			]),
			new TextField('DATA', [
				'serialized' => true,
				'menu-hidden' => true
			])
		];
	}
}
