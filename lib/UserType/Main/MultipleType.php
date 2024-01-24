<?php

namespace Wilp\UserType\Main;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\HtmlFilter;
use CUserTypeManager;
use Bitrix\Main\UserField\Types\BaseType;

Loc::loadMessages(__FILE__);
/**
 * Class StringType
 * @package Bitrix\Main\UserField\Types
 */
class MultipleType extends BaseType
{
	public const
		USER_TYPE_ID = 'multiply',
		RENDER_COMPONENT = 'raketa:main.field.multiply';

	public static function getDescription(): array
	{
		return [
			'DESCRIPTION' => 'Составное поле',
			'BASE_TYPE' => CUserTypeManager::BASE_TYPE_STRING
		];
	}

	public static function onAfterFetch($arUserField, $value)
	{
		$result = $value['VALUE'];
		if(!empty($result)) {
			$result = unserialize($result);
		}
		return $arUserField["MULTIPLE"] == "Y" ? $result : [$result];
	}

	/**
	 * This function is called when new properties are added. We only support mysql data types.
	 *
	 * This function is called to construct the SQL column creation query
	 * to store non-multiple property values.
	 * Values of multiple properties are not stored in rows, but in columns
	 * (as in infoblocks) and the type of such a field in the database is always text
	 *
	 * @return string
	 */
	public static function getDbColumnType(): string
	{
		return 'text';
	}

	/**
	 * This function is called before saving the property metadata to the database.
	 *
	 * It should 'clear' the array with the settings of the instance of the property type.
	 * In order to accidentally / intentionally no one wrote down any garbage there.
	 *
	 * @param array $userField An array describing the field. Warning! this description of the field has not yet been saved to the database!
	 * @return array An array that will later be serialized and stored in the database.
	 */
	public static function prepareSettings(array $userField): array
	{
		$additional_options = $userField['SETTINGS']['ADDITIONAL_OPTIONS'];

		return [
			'ADDITIONAL_OPTIONS' => $additional_options,
		];
	}

	/**
	 * @param null|array $userField
	 * @param array $additionalSettings
	 * @return array
	 */
	public static function getFilterData(?array $userField, array $additionalSettings): array
	{
		return [
			'id' => $additionalSettings['ID'],
			'name' => $additionalSettings['NAME'],
			'filterable' => ''
		];
	}

	/**
	 * This function is validator.
	 * Called from the CheckFields method of the $ USER_FIELD_MANAGER object,
	 * which can be called from the Add / Update methods of the property owner entity.
	 * @param array $userField
	 * @param string|array $value
	 * @return array
	 */
	public static function checkFields(array $userField, $value): array
	{
		$fieldName = HtmlFilter::encode(
			$userField['EDIT_FORM_LABEL'] <> ''
				? $userField['EDIT_FORM_LABEL'] : $userField['FIELD_NAME']
		);

		$msg = [];
		return $msg;
	}

	/**
	 * This function should return a representation of the field value for the search.
	 * It is called from the OnSearchIndex method of the object $ USER_FIELD_MANAGER,
	 * which is also called the update function of the entity search index.
	 * For multiple values, the VALUE field is an array.
	 * @param array $userField
	 * @return string|null
	 */
	public static function onSearchIndex(array $userField): ?string
	{
		if(is_array($userField['VALUE']))
		{
			$result = implode('\r\n', $userField['VALUE']);
		}
		else
		{
			$result = $userField['VALUE'];
		}

		return $result;
	}

	//<editor-fold desc="Events and methods..."  defaultstate="collapsed">
	/**
	 * You can register the onBeforeGetPublicView event handler
	 * and customize the display by manipulating the metadata of a custom property.
	 * \Bitrix\Main\EventManager::getInstance()->addEventHandler(
	 * 'main',
	 * 'onBeforeGetPublicView',
	 * array('CUserTypeString', 'onBeforeGetPublicView')
	 * );
	 * You can do the same for editing:
	 * onBeforeGetPublicEdit (EDIT_COMPONENT_NAME � EDIT_COMPONENT_TEMPLATE)
	 */
	/*
		public static function onBeforeGetPublicView($event)
		{
			$params = $event->getParameters();
			$arUserField = &$params[0];
			$arAdditionalParameters = &$params[1];
			if ($arUserField['USER_TYPE_ID'] == 'string')
			{
				$arUserField['VIEW_COMPONENT_NAME'] = 'my:system.field.view';
				$arUserField['VIEW_COMPONENT_TEMPLATE'] = 'string';
			}
		}
	*/

	/**
	 * You can register the onGetPublicView event handler
	 * and display the property as you need.
	 * \Bitrix\Main\EventManager::getInstance()->addEventHandler(
	 * 'main',
	 * 'onGetPublicView',
	 * array('CUserTypeString', 'onGetPublicView')
	 * );
	 * You can do the same for editing: onGetPublicEdit
	 */
	/*
		public static function onGetPublicView($event)
		{
			$params = $event->getParameters();
			$arUserField = $params[0];
			$arAdditionalParameters = $params[1];
			if ($arUserField['USER_TYPE_ID'] == 'string')
			{
				$html = 'demo string';
				return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $html);
			}
		}
	*/

	/**
	 * You can register the onAfterGetPublicView event handler
	 * and modify the html before displaying it.
	 * \Bitrix\Main\EventManager::getInstance()->addEventHandler(
	 * 'main',
	 * 'onAfterGetPublicView',
	 * array('CUserTypeString', 'onAfterGetPublicView')
	 * );
	 * You can do the same for editing: onAfterGetPublicEdit
	 */
	/*
		public static function onAfterGetPublicView($event)
		{
			$params = $event->getParameters();
			$arUserField = $params[0];
			$arAdditionalParameters = $params[1];
			$html = &$params[2];
			if ($arUserField['USER_TYPE_ID'] == 'string')
			{
				$html .= '!';
			}
		}
	*/

	/**
	 * This function is called before storing the values in the database.
	 * Called from the Update method of the $ USER_FIELD_MANAGER object.
	 * For multiple values, the function is called several times.
	 * @param array $arUserField
	 * @param $value
	 * @return string
	 */

	static function OnBeforeSave($arUserField, $value)
	{
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		$postDataPropDelete = $request->getPost($arUserField['FIELD_NAME'] . '_del');


		if($arUserField['MULTIPLE'] === 'Y' && isset($value['INDEX'])) {
			$index = explode('[', $value['INDEX']);
			$index = $index[count($index) - 1];
			$index = str_replace(']', '', $index);
			$deleteFileArray = $postDataPropDelete[$index]['OPTIONS'] ?? [];
		} else {
			$deleteFileArray = $postDataPropDelete['OPTIONS'] ?? [];
		}

		$value = self::ConvertToDB(
			$arUserField,
			[
				'VALUE' => $value
			],
			$deleteFileArray,
			[
				'module' => 'main'
			]
		)['VALUE'];

		return $value;
	}

	public static function ConvertToDB($arProperty, $value, $deleteFileArray = [], $options = []) //+ BD
	{
		$arResultOptions = [];
		$module = $options['module'] ?? 'iblock';
		if(!empty($value['VALUE']['OPTIONS'])) {
			foreach ($value['VALUE']['OPTIONS'] as $OPTION_KEY => $OPTION_VALUE) {

				$OPTION_TYPE = $value['VALUE']['OPTIONS_TYPE'][$OPTION_KEY];
				$VALUE = $OPTION_VALUE;

				if($OPTION_TYPE === 'bitrixfile') {
					$deleteFilesIn = [];
					if(!empty($deleteFileArray)) {
						$deleteFilesIn = $deleteFileArray[$OPTION_KEY] ?? [];
					}
					$VALUE = self::fileWork($OPTION_VALUE, $deleteFilesIn, $module);
				}

				if(!empty($VALUE) || $VALUE === 0 || $VALUE === '0') {
					$arResultOptions[$OPTION_KEY] = [
						'VALUE' => $VALUE,
						'TYPE' => $OPTION_TYPE,
						'KEY' => $OPTION_KEY
					];
				}
			}
		}

		$result = false;

		if(!empty($arResultOptions)) {
			$result = [];
			$result['VALUE'] = serialize($arResultOptions);
			$result['DESCRIPTION'] = '';
		}


		return $result;
	}

	public static function fileWork($filesMass, $deleteFiles, $module = 'iblock')
	{
		$arResultFile = [];
		if(!empty($filesMass) && is_array($filesMass)) {
			$i = 0;
			if(!empty($deleteFiles)) {
				foreach ($deleteFiles as $key => $delStatus) {
					if(!empty($filesMass[$key]) && $delStatus === 'Y') {
						\CFile::Delete($filesMass[$key]);
						unset($filesMass[$key]);
					}
				}
			}
			foreach ($filesMass as $file) {
				if(is_array($file) && !empty($file['tmp_name'])) {
					$file = \CIBlock::makeFileArray($file);
					$file['MODULE_ID'] = $module;
					$id = \CFile::SaveFile($file, $module);
				} else {
					$id = $file;
				}
				if(!empty($id)) {
					$arResultFile[$i++] = $id;
				}
			}
		}

		return $arResultFile;
	}


	//</editor-fold>
}
