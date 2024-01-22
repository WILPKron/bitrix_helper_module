<?php

namespace Raketa\Plastfoil\Base;

use Bitrix\Main\EventManager;
use Raketa\Plastfoil\Helpers\Main as HelperMain;
use Bitrix\Main\Loader;

/**
 * Class IBlockOneElement
 * @package dnext
 * Для инфоблоков с одним элементом = переход на редактирование записи с $ELEMENT_ID
 */

class IBlockOneElement
{
    public static $IBLOCK_TYPE;
    public static $IBLOCK_ID = 0;
    public static $ELEMENT_ID = 0;

    public function __construct($iblockType, $iblockCode)
    {

		$iblockID = 0;
		$elementID = 0;

		if (!Loader::includeModule('iblock')) {
			return false;
		}

		$ib_list = \CIBlock::GetList([], [
			"CODE" => $iblockCode,
			"TYPE" => $iblockType,
		]);
		while ($ib = $ib_list->fetch()) {
			$iblockID = $ib['ID'];
			$dbElement = \CIBlockElement::GetList([],
				[
					'IBLOCK_ID' => $iblockID
				], false, [
					'nPageSize' => 1
				], ['ID']
			);
			while ($element = $dbElement->fetch()) {
				$elementID = $element['ID'];
			}
		}

		if($iblockID > 0 && $elementID > 0) {
			static::run($iblockID, $elementID, $iblockType);
		}
    }

    public static function run($IBLOCK_ID, $ELEMENT_ID, $IBLOCK_TYPE)
    {
		$options = [
			'ELEMENT_ID' => $ELEMENT_ID,
			'IBLOCK_TYPE' => $IBLOCK_TYPE,
			'IBLOCK_ID' => $IBLOCK_ID,
		];
		EventManager::getInstance()->addEventHandler("iblock", "OnBeforeIBlockElementAdd", function (&$arFields) use ($options) {
			if(self::disallowAddingItems($arFields, $options) === false) {
				return false;
			}
		});
        EventManager::getInstance()->addEventHandler("iblock", "OnBeforeIBlockElementDelete", function ($ID) use ($options)  {
			self::disallowElementDelete($ID, $options);
		});
		EventManager::getInstance()->AddEventHandler('iblock','OnAfterIBlockElementUpdate', function (&$arFields) use ($options)  {
			self::updateElement($arFields, $options);
		});
        EventManager::getInstance()->addEventHandler("main", "OnBuildGlobalMenu", function (&$aGlobalMenu, &$aModuleMenu) use ($options)  {
			self::immediatelyElementEdit($aGlobalMenu, $aModuleMenu, $options);
		});
        EventManager::getInstance()->addEventHandler("main", "OnBeforeLocalRedirect", function (&$url, $skipSecurityCheck, $bExternal) use ($options)  {
			self::disallowRedirectToElementsList($url, $skipSecurityCheck, $bExternal, $options);
		});
        EventManager::getInstance()->addEventHandler("main", "OnAdminContextMenuShow", function (&$items) use ($options)  {
			self::hideButtons($items, $options);
		});
		EventManager::getInstance()->AddEventHandler('main','OnAdminTabControlBegin', function (&$TabControl) use ($options)  {
			self::RemoveYandexDirectTab($TabControl, $options);
		});
    }

    public static function thisClass()
    {
        return __CLASS__;
    }

    /**
     * Запрет добавления других элементов в этот инфоблок
     *
     * @param $arFields
     */
    public static function disallowAddingItems(&$arFields, $options){}

    /**
     * запрет удаления элемента
     *
     * @param $ID
     * @return bool
     */
    public static function disallowElementDelete($ID, $options)
    {
        if($ID == $options['ELEMENT_ID']){
			HelperMain::getApp()->throwException("Этот элемент нельзя удалить.");
            return false;
        }
		return true;
    }

	public static function updateElement(&$arFields, $options) {
		if($options['IBLOCK_ID'] === $arFields['IBLOCK_ID']) {
			$arField = [
				'NAME' => $arFields['NAME']
			];
			$ibInstance = new \CIBlock;
			$ibInstance->Update($arFields['IBLOCK_ID'], $arField);
		}
	}

	/**
	 * По клику на инфоблок переход сразу на редактирование элемента
	 *
	 * @param $aGlobalMenu
	 * @param $aModuleMenu
	 * @param $options
	 */
    public static function immediatelyElementEdit(&$aGlobalMenu, &$aModuleMenu, $options)
    {
        foreach ($aModuleMenu as &$arMenu) {
            if($arMenu["items_id" ] == "menu_iblock_/" . $options['IBLOCK_TYPE']) {
                foreach ($arMenu["items"] as &$arMenu2) {
                    if($arMenu2["items_id"] == "menu_iblock_/" . $options['IBLOCK_TYPE'] . "/" . $options['IBLOCK_ID']) {
						$arMenu2['icon'] = 'default_menu_icon';
                        $arMenu2["url"] = "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $options['IBLOCK_ID'] . "&type=" . $options['IBLOCK_TYPE'] . "&ID=". $options['ELEMENT_ID'] . "&lang=ru&find_section_section=-1&WF=Y";
                        $arMenu2["items"] = array();
                        $arMenu2["dynamic"] = false;
                        break;
                    }
                }
                unset($arMenu2);
                break;
            }
        }
        unset($arMenu);
    }

    /**
     * Отменяем редирект на список элементов после сохранения элемента
     *
     * @param $url
     * @param $skipSecurityCheck
     * @param $bExternal
     */
    public static function disallowRedirectToElementsList(&$url, $skipSecurityCheck, $bExternal, $options)
    {
        $app = HelperMain::getApp();

        if (in_array($app->GetCurPage(), array("/bitrix/admin/iblock_element_edit.php",)) && $_REQUEST["IBLOCK_ID"] == $options['IBLOCK_ID']) {
            $url = $app->GetCurPageParam() . "&ID=" . $options['ELEMENT_ID'];
        }
    }

	/**
	 * Убираем для этого инфоблока контекстные кнопки
	 *
	 * @param $items
	 * @param $options
	 */
    public static function hideButtons(&$items, $options)
    {
        $app = HelperMain::getApp();
        $listScripts = ["/bitrix/admin/iblock_element_admin.php", "/bitrix/admin/iblock_element_edit.php"];

        if (in_array($app->GetCurPage(), $listScripts) && $_REQUEST["IBLOCK_ID"] == $options['IBLOCK_ID']) {
            $items = [];
        }
    }

    /**
     * Убрать вкладку реклама
     *
     * @param $TabControl
     */
    public static function RemoveYandexDirectTab(&$TabControl, $options)
    {
        $app = HelperMain::getApp();
        if ($app->GetCurPage()=='/bitrix/admin/iblock_element_edit.php' || $app->GetCurPage()=='/bitrix/admin/cat_product_edit.php') {
            foreach($TabControl->tabs as $Key => $arTab) {
                if (in_array($arTab['DIV'], ['seo_adv_seo_adv'])) {
                    unset($TabControl->tabs[$Key]);
                }
            }
        }
    }
}
