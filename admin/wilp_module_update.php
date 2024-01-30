<?php
/** @global CMain $APPLICATION */
/** @global CDatabase $DB */
/** @global CUser $USER */

use Bitrix\Main\Loader,
    Bitrix\Main,
    Bitrix\Iblock,
    Bitrix\Currency,
    Bitrix\Catalog;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
Loader::includeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");
CJSCore::Init(["jquery"]);
$APPLICATION->SetTitle('Обновление модуля');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

    <div class="wilp-update">

    </div>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>
