<?php

namespace Wilp\Events;

use Bitrix\Main\Entity;
use Wilp\Helpers\IBlockLID;
use Wilp\Helpers\Image\ImageResizeByInterventionImage;
use Bitrix\Main\EventManager;
use Wilp\Table\OnePageIBlockTable;
use Wilp\Base\IblockOneElement;
use Bitrix\Main\ORM\Data\DataManager;
use Wilp\Model\SectionDocumentsTable;
class EventsRun
{
	static public function run() {
		self::all();

		if(\CSite::InDir('/bitrix/admin/')) {
			self::admin();
		} else {
			// self::user();
		}
		self::initModuleFields();
	}

	/**
	 * Метод для установки событий в административной части сайта
	**/

	static private function admin()
	{
		self::initOneElementIblock();
		EventManager::getInstance()->addEventHandler('main', 'OnFileDelete', function ($arFile) {
			if(!empty($arFile['SUBDIR'])) {
				$pathResizeDirServer = ImageResizeByInterventionImage::getPathResizeDirServer($arFile['SUBDIR']);
				if(is_dir($pathResizeDirServer)) {
					ImageResizeByInterventionImage::clearImageConvectorIBlock($pathResizeDirServer);
					rmdir($pathResizeDirServer);
				}
			}
		});
	}

	/**
	 * Метод для установки событий в публичной части сайта
	 **/

	static private function user()
	{
		EventManager::getInstance()->addEventHandler("main", "OnEndBufferContent", function (&$content) {
			global $USER;
			if(!\CSite::InDir('/bitrix/') && !$USER->IsAdmin()) {
				$arPatternsToRemove = Array(
					'/<link.+?href=".+?kernel_main\/kernel_main_v1\.css\?\d+"[^>]+>/',
					'/<link.+?href=".+?bitrix\/panel\/main\/popup.css\?\d+"[^>]+>/',
					'/<link.+?href=".+?bitrix\/js\/main\/core\/css\/core[^"]+"[^>]+>/',
					'/<script.+?>if\(\!window\.BX\)window\.BX.+?<\/script>/',
					'/<script[^>]+?>\(window\.BX\|\|top\.BX\)\.message[^<]+<\/script>/',
					'/<script.+?src=".+?bitrix\/js\/main\/core\/core[^"]+"><\/script\>/',
					'/<script.+?src=".+?bitrix\/js\/main\/pageobject\/pageobject.js\?\d+"><\/script>/',
					'/<script.+?>BX\.(setCSSList|setJSList)\(\[.+?\]\).*?<\/script>/',
					'/BX\.(setCSSList|setJSList)\(\[.+?\]\);/'
				);
				$content = preg_replace($arPatternsToRemove, "", $content);
			}
			$content = str_replace(array("\r","\n")," ",$content);
			$content = str_replace(array("> <"),"><",$content);
			$content = preg_replace(array('/<font class="tablebodytext">.*?<\/font><\/font>/'), "", $content);
		});
	}

	static public function initOneElementIblock()
	{
		$res = OnePageIBlockTable::getList();
		foreach ($res->fetchAll() as $element) {
			if(!empty($element['UF_IBLOCK_TYPE']) && !empty($element['UF_IBLOCK_CODE'])) {
				new IblockOneElement($element['UF_IBLOCK_TYPE'], $element['UF_IBLOCK_CODE']);
			}
		}
	}
	static public function initModuleFields()
	{
		$fieldsClassName = [
			"UserTypeMultiple",
			"UserTypeTable",
		];
		$instance = EventManager::getInstance();
		foreach ($fieldsClassName as $className) {
			$instance->addEventHandler("iblock", "OnIBlockPropertyBuildList", [
				"\\Wilp\\UserType\\Iblock\\$className",
				'GetUserTypeDescription'
			]);
		}

		 $instance->addEventHandler("main", "OnUserTypeBuildList", [
		 	"\\Wilp\\UserType\\Main\\CUserMultipleType",
		 	'GetUserTypeDescription'
		 ]);
	}
	/**
	 * Метод для установки событий
	 **/
	static public function all()
	{

		EventManager::getInstance()->addEventHandler('iblock', 'OnAfterIBlockSectionAdd', function (&$arFields) {
			$iblockId = IBlockLID::IBlockIDByCode(IBlockLID::$IBLOCK_FILE_LIBRARY_GEREEAL);
			if ($arFields["IBLOCK_ID"] == $iblockId && $arFields["ID"] > 0) {
				\Wilp\Table\WilpDocumentsSectionsLinkTable::add([
					'IBLOCK_SECTION_PARENT_ID' => $arFields["ID"],
					'IBLOCK_SECTION_CHILD_ID' => 0
				]);
			}
		});
		EventManager::getInstance()->addEventHandler('iblock', 'OnAfterIBlockSectionDelete', function (&$arFields) {
			$iblockId = IBlockLID::IBlockIDByCode(IBlockLID::$IBLOCK_FILE_LIBRARY_GEREEAL);
			if ($arFields["IBLOCK_ID"] == $iblockId && $arFields["ID"] > 0) {
				\Wilp\Table\WilpDocumentsSectionsLinkTable::deleteAll($arFields['ID']);
			}
		});


		include_once __DIR__ . '../../../globalFunction.php';
	}
}
