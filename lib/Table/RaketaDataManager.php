<?php

namespace Raketa\Plastfoil\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Highloadblock\HighloadBlockLangTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Raketa\Plastfoil\Helpers\Main;
use Bitrix\Highloadblock as HL;

abstract class RaketaDataManager extends DataManager
{
	static $LAST_ERROR = [];

	public static function getIdHLEntity()
	{
		if(Loader::includeModule("highloadblock"))
		{
			$table = HighloadBlockTable::getList([
				'filter' => [
					'TABLE_NAME' => static::getTableName()
				],
				'select' => ['ID']
			])->fetch();

			$hlblock = HL\HighloadBlockTable::getById($table['ID'])->fetch();
			$entity = HL\HighloadBlockTable::compileEntity($hlblock);
			return $entity->getDataClass();
		}
		return null;
	}
	public static function createTable($options = []) {

		if(!Loader::includeModule("highloadblock") || empty($options['admin'])) {
			$connection = \Bitrix\Main\Application::getInstance()->getConnection();
			if ($connection->isTableExists(static::getTableName())) {
				return false;
			}
			static::getEntity()->createDbTable();
			return true;
		}

		$object = HighloadBlockTable::add([
			'NAME' => str_replace('Table', '', $options['admin']['name']),
			'TABLE_NAME' => str_replace('Table', '', static::getTableName())
		]);

		foreach (static::getMap() as $field) {
			$type = str_replace('\\','', $field->getGetterTypeHint());
			switch ($type) {
				case 'int': $type = 'integer'; break;
				case 'BitrixMainTypeDate': $type = 'datetime'; break;
			}
			$arFields = [
				'FIELD_NAME' => $field->getName(),
				'USER_TYPE_ID' => $type,
				'MANDATORY' => $field->getParameter('required') ? "Y" : "N",
				'MULTIPLE' => $field->getParameter('serialized') ? "Y" : "N",
				"EDIT_FORM_LABEL" => ['ru' => $field->getTitle()],
				"LIST_COLUMN_LABEL" => ['ru' => $field->getTitle()],
				"LIST_FILTER_LABEL" => ['ru' => $field->getTitle()],
			];

			$oUserTypeEntity = new \CUserTypeEntity();
			$arFields['ENTITY_ID'] = 'HLBLOCK_' . $object->getId();
			$id = $oUserTypeEntity->Add($arFields);
			if (!$id) {
				self::$LAST_ERROR[] = Main::getApp()->GetException();
			}
		}

		if(!empty($options['lang']) && empty($object->getErrors())) {
			foreach ($options['lang'] as $lang => $name) {
				HighloadBlockLangTable::add([
					'ID' => $object->getId(),
					'LID' => $lang,
					'NAME' => $name
				]);
			}
		} else {
			foreach ($object->getErrors() as $error) {
				dump($error->getMessage());
			}
			return false;
		}
		return true;
	}

	public static function dropTable(){
		$connection = \Bitrix\Main\Application::getInstance()->getConnection();
		if(Loader::includeModule("highloadblock")) {
			$table = HighloadBlockTable::getList([
				'filter' => [
					'TABLE_NAME' => static::getTableName()
				]
			])->fetch();
			if(!empty($table['ID'])) {
				HighloadBlockTable::delete($table['ID']);
				return true;
			}
		}
		if ($connection->isTableExists(static::getTableName())) {
			$connection->dropTable(static::getTableName());
		}
		return true;
	}
	public static function truncateTable() {
		$connection = \Bitrix\Main\Application::getInstance()->getConnection();

		if(Loader::includeModule("highloadblock")) {
			$table = HighloadBlockTable::getList([
				'filter' => [
					'TABLE_NAME' => static::getTableName()
				]
			])->fetch();
			if(!empty($table['ID'])) {
				foreach (static::getMap() as $field) {
					if($field->getParameter('serialized')) {
						$tableName = static::getTableName() . '_' . mb_strtolower($field->getName());
						if ($connection->isTableExists($tableName)) {
							$connection->truncateTable($tableName);
						}
					}
				}
			}
		}

		$connection->truncateTable(static::getTableName());

		return true;
	}
}
