<?php
namespace Raketa\Plastfoil\Table\PageInfo;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ORM\Data\DataManager,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\TextField;

Loc::loadMessages(__FILE__);

/**
 * Class Table
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> UF_TITLE text optional
 * <li> UF_LINK text optional
 * <li> UF_IMAGE_LINK int optional
 * <li> UF_LANG int optional
 * </ul>
 *
 * @package Bitrix\
 **/

class ServicesPageTable extends DataManager
{
	public static $i = 0;
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'servicespage';
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
					'title' => Loc::getMessage('_ENTITY_ID_FIELD')
				]
			),
			new TextField(
				'UF_TITLE',
				[
					'title' => Loc::getMessage('_ENTITY_UF_TITLE_FIELD')
				]
			),
			new TextField(
				'UF_LINK',
				[
					'title' => Loc::getMessage('_ENTITY_UF_LINK_FIELD')
				]
			),
			new IntegerField(
				'UF_IMAGE_LINK',
				[
					'title' => Loc::getMessage('_ENTITY_UF_IMAGE_LINK_FIELD'),
					'fetch_data_modification' => function() {
						return [
							function($value) {
								if(!empty($value)) {
									return \CFile::GetFileArray($value);
								}
								return $value;
							}
						];
					}
				]
			),
			new IntegerField(
				'UF_LANG',
				[
					'title' => Loc::getMessage('_ENTITY_UF_LANG_FIELD'),
					'fetch_data_modification' => function() {
						return [
							function ($value) {
								$rsType = \CUserFieldEnum::GetList([], [
									'USER_FIELD_NAME' => 'UF_LANG',
									'ID' => $value
								]);
								if(!empty($rsType->arResult[0])) {
									return $rsType->arResult[0];
								}
								return $value;
							}
						];
					}
				]
			),
		];
	}
}
