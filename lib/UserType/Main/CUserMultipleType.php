<?php

namespace Raketa\Plastfoil\UserType\Main;

use Bitrix\Main\Loader;
use Bitrix\Main\UserField\TypeBase;

/**
 * Class CUserTypeString
 * @deprecated deprecated since main 20.0.700
 */
class CUserMultipleType extends TypeBase
{
	const USER_TYPE_ID = MultipleType::USER_TYPE_ID;

	public static function getUserTypeDescription()
	{
		return MultipleType::getUserTypeDescription();
	}

	public static function getPublicView($userField, $additionalParameters = array())
	{
		return MultipleType::renderView($userField, $additionalParameters);
	}

	public static function getPublicEdit($userField, $additionalParameters = array())
	{
		return MultipleType::renderEdit($userField, $additionalParameters);
	}

	function getSettingsHtml($userField, $additionalParameters, $varsFromForm)
	{
		return MultipleType::renderSettings($userField, $additionalParameters, $varsFromForm);
	}

	function getEditFormHtml($userField, $additionalParameters)
	{
		return MultipleType::renderEditForm($userField, $additionalParameters);
	}

	function getAdminListViewHtml($userField, $additionalParameters)
	{
		return MultipleType::renderAdminListView($userField, $additionalParameters);
	}

	function getAdminListEditHtml($userField, $additionalParameters)
	{
		return MultipleType::renderAdminListEdit($userField, $additionalParameters);
	}

	function getFilterHtml($userField, $additionalParameters)
	{
		return MultipleType::renderFilter($userField, $additionalParameters);
	}

	public static function getDbColumnType()
	{
		return MultipleType::getDbColumnType();
	}

	function getFilterData($userField, $additionalParameters)
	{
		return MultipleType::getFilterData($userField, $additionalParameters);
	}

	function prepareSettings($userField)
	{
		return MultipleType::prepareSettings($userField);
	}

	function checkFields($userField, $value)
	{
		return MultipleType::checkFields($userField, $value);
	}

	function onSearchIndex($userField)
	{
		return MultipleType::onSearchIndex($userField);
	}
}
