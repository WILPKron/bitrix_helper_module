<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class wilpkron_bitrix_helper extends CModule
{
	private static $AJAX_FOLDER = __DIR__ . '/../../../../ajax/';
	private static $AJAX_FILE = __DIR__ . '/../ajax.php';

	public function __construct()
	{
		$arModuleVersion = array();

		include __DIR__ . '/version.php';

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->MODULE_ID = 'wilpkron.bitrix_helper';
		$this->MODULE_NAME = Loc::getMessage('RP_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('RP_MODULE_DESCRIPTION');
		$this->MODULE_GROUP_RIGHTS = 'N';
		$this->PARTNER_NAME = Loc::getMessage('RP_MODULE_PARTNER_NAME');
		$this->PARTNER_URI = 'http://bitrix.expert';
	}

	public function doInstall()
	{
		$eventManager = EventManager::getInstance();
        ModuleManager::registerModule($this->MODULE_ID);
		$eventManager->registerEventHandlerCompatible("main", "OnPageStart", $this->MODULE_ID, "\\Wilp\\Events\\EventsRun", "run");

		if(Loader::includeModule($this->MODULE_ID)) {
			\Wilp\Table\Menu\MenuEntityTable::createTable();
		}

		$this->InstallFiles();
	}

	public function doUninstall()
	{
		$eventManager = EventManager::getInstance();
		$this->UnInstallFiles();
		//$eventManager->unRegisterEventHandler("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "\\Wilp\\UserType\\RaketaUserTypeTable", "GetUserTypeDescription");
		$eventManager->unRegisterEventHandler("main", "OnPageStart", $this->MODULE_ID, "\\Wilp\\Events\\EventsRun", "run");
		ModuleManager::unRegisterModule($this->MODULE_ID);
	}

	public function InstallFiles()
	{
		if(file_exists(self::$AJAX_FILE)) {
			if(!is_dir(self::$AJAX_FOLDER)) {
				mkdir(self::$AJAX_FOLDER, 0755);
			}
			file_put_contents(self::$AJAX_FOLDER . 'index.php', file_get_contents(self::$AJAX_FILE));
		}
		CopyDirFiles(__DIR__ . '/admin/', $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		return true;
	}

	public function UnInstallFiles()
	{
		if(file_exists(self::$AJAX_FOLDER . 'index.php')) {
			unlink(self::$AJAX_FOLDER . 'index.php');
		}
		if(is_dir(self::$AJAX_FOLDER)) {
			rmdir(self::$AJAX_FOLDER);
		}
		DeleteDirFiles(__DIR__ . '/admin/', $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		return true;
	}
}
