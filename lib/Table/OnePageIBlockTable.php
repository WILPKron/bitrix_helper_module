<?php
namespace Wilp\Table;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\TextField;

/**
 * Class OneTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> UF_IBLOCK_CODE text optional
 * <li> UF_IBLOCK_TYPE text optional
 * </ul>
 *
 * @package Bitrix\One
 **/

class OnePageIBlockTable extends WilpDataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'iblock_one';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => ''
				]
			),
			new TextField(
				'UF_IBLOCK_CODE',
				[
					'title' => ''
				]
			),
			new TextField(
				'UF_IBLOCK_TYPE',
				[
					'title' => ''
				]
			),
		];
	}
}
