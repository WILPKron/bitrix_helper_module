<?php
/** @global CMain $APPLICATION */
/** @global CDatabase $DB */
/** @global CUser $USER */

use Bitrix\Main\Loader,
	Bitrix\Main,
	Bitrix\Iblock,
	Bitrix\Currency,
	Bitrix\Catalog,
	Wilp\Model\SectionDocumentsTable,
	Wilp\Table\RaketaDocumentsSectionsLinkTable;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
Loader::includeModule("iblock");
Loader::includeModule("raketa.plastfoil");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");

$request = Main\Context::getCurrent()->getRequest();

if(
	!empty(
		$_REQUEST['action_button_tbl_iblock_section_'.$_REQUEST['bxajaxid']]
	)
) {
	switch ($_REQUEST['action_button_tbl_iblock_section_'.$_REQUEST['bxajaxid']]) {
		case 'delete':
			if (CIBlockSectionRights::UserHasRightTo($_REQUEST['IBLOCK_ID'], $_REQUEST['ID'], "section_delete"))
			{
				@set_time_limit(0);
				$DB->StartTransaction();
				if (!CIBlockSection::Delete($_REQUEST['ID'])) {
					if ($e = $APPLICATION->GetException()) {
						$message = $e->GetString();
					} else {
						$message = GetMessage("IBSEC_A_DELERR_REFERERS");
					}
					$DB->Rollback();
				}
				else {
					$DB->Commit();
				}
			}
		break;
	}
}


$ajaxId = \CAjax::getComponentID('bitrix:main.ui.grid', '.default', '');

$grid_options = new Bitrix\Main\Grid\Options('report_list');
$sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();

$backParentID = false;
$IBlockID = false;



if(!empty($_GET['SECTION_ID'])) {
	$res = SectionDocumentsTable::getList([
		'filter' => [
			'ID' => $_GET['SECTION_ID']
		],
		'select' => ['ID', 'NAME', 'IBLOCK_ID', 'CODE', 'UF_FILE', 'ACTIVE', 'CHILD_ID' => 'CHILD_SECTIONS.ID', 'PRENT_ID' => 'PARENT_SECTIONS.ID']
	])->fetchAll();
	$childList = [];
	foreach ($res as $item) {
		$data = [];
		$childList[] = $item['CHILD_ID'];
		if($backParentID === false && !empty($item['PRENT_ID'])) {
			$backParentID = $item['PRENT_ID'];
		}
		if($IBlockID === false) {
			$IBlockID = $item['IBLOCK_ID'];
		}
		$item = $data;
	}

	$res = SectionDocumentsTable::getList([
		'filter' => [
			'ID' => $childList
		],
		'select' => ['ID', 'NAME', 'IBLOCK_ID', 'CODE', 'UF_FILE' ]
	])->fetchAll();

} else {
	$sectionWhithoutParent = RaketaDocumentsSectionsLinkTable::sectionWhithoutParent();
	$res = SectionDocumentsTable::getList([
		'filter' => [
			'ID' => array_map(fn($item) => $item['ID'], $sectionWhithoutParent)
		],
		'select' => ['ID', 'NAME', 'IBLOCK_ID', 'CODE', 'UF_FILE', 'ACTIVE']
	])->fetchAll();
}

$list = [];

$linkParamsOut = 'IBLOCK_ID='. $IBlockID .'&lang=ru';
if(!empty($_GET['PARENT_ID'])) {
	$linkParamsOut .= '&SECTION_ID='. $_GET['PARENT_ID'];

} else if($backParentID) {
	$linkParamsOut .= '&SECTION_ID='. $backParentID;
}

if(!empty($_GET['SECTION_ID'])) {
	$linkParamsNew = 'IBLOCK_ID='. $IBlockID .'&lang=ru';
	$linkParamsNew .= '&PARENT_ID='. $_GET['SECTION_ID'];
} else {
	$linkParamsNew = 'IBLOCK_ID='. $res[0]['IBLOCK_ID'] .'&lang=ru';
}


foreach ($res as $item) {

	$deleteAction = "if (confirm('Будет удалена вся информация, связанная с этой записью! Продолжить?')) ";
	$deleteAction .= "BX.Main.gridManager.getById('tbl_iblock_section_".$ajaxId."')";
	$deleteAction .= ".instance.reloadTable('POST', {";
		$deleteAction .= "'action_button_tbl_iblock_section_".$ajaxId."': 'delete',";
		$deleteAction .= "'ID': '".$item['ID']."',";
		$deleteAction .= "'IBLOCK_ID': '".$item['IBLOCK_ID']."',";
		$deleteAction .= "'type': 'file_library',";
		$deleteAction .= "'lang': 'ru'";
	$deleteAction .= "})";

	$linkParams = 'IBLOCK_ID='. $item['IBLOCK_ID'] .'&lang=ru&SECTION_ID='. $item['ID'];
	if(!empty($_GET['SECTION_ID'])) {
		$linkParams .= '&PARENT_ID='. $_GET['SECTION_ID'];
	}

	$class = !empty($item['UF_FILE']) ? 'default_menu_icon' : 'iblock-section-icon';
	$list[] = [
		'data' => $item,
		'columns' => [
			'NAME' => '<a href="/bitrix/admin/raketa_document_redactore.php?'.$linkParams.'">
			<span class="adm-submenu-item-link-icon adm-list-table-icon '. $class .' "></span>
			'.$item['NAME'].'
			</a>',
		],
		'actions' => [
			[
				'text'    => 'Редактировать',
				'onclick' => 'document.location.href="/bitrix/admin/raketa_document_editor.php?'.$linkParams.'"'
			],
			[
				'text'    => "Удалить раздел",
				'onclick' => $deleteAction
			]
		]
	];
}

$nav = new Bitrix\Main\UI\PageNavigation('report_list');
$nav->allowAllRecords(true)
	->setPageSize($nav_params['nPageSize'])
	->initFromUri();

$onchange = new \Bitrix\Main\Grid\Panel\Snippet\Onchange();
$onchange->addAction(
	[
		'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CALLBACK,
		'CONFIRM' => true,
		'CONFIRM_APPLY_BUTTON'  => 'Подтвердить',
		'DATA' => [
			['JS' => 'Grid.removeSelected()']
		]
	]
);

CJSCore::Init(["jquery"]);
$APPLICATION->SetTitle('Одностарничные инфоблоки');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?php

$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
	'GRID_ID' => 'tbl_iblock_section_' . $ajaxId,
	'COLUMNS' => [
		['id' => 'NAME', 'name' => 'Название', 'sort' => 'NAME', 'default' => true],
		['id' => 'ACTIVE', 'name' => 'Активный', 'sort' => 'ACTIVE', 'default' => true],
		['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
	],
	'ROWS' => $list, //Самое интересное, опишем ниже
	'SHOW_ROW_CHECKBOXES' => false,
	// 'NAV_OBJECT' => $nav,
	'AJAX_MODE' => 'N',
	'AJAX_ID' => $ajaxId,
	'AJAX_OPTION_JUMP'          => 'N',
	'SHOW_CHECK_ALL_CHECKBOXES' => false,
	'SHOW_ROW_ACTIONS_MENU'     => true,
	'SHOW_GRID_SETTINGS_MENU'   => true,
	'SHOW_NAVIGATION_PANEL'     => false,
	'SHOW_PAGINATION'           => false,
	'SHOW_SELECTED_COUNTER'     => true,
	'SHOW_TOTAL_COUNTER'        => true,
	'SHOW_PAGESIZE'             => true,
	'SHOW_ACTION_PANEL'         => true,
	'ACTION_PANEL' => [
		'GROUPS' => [
			[
				'ITEMS' => [
					[
						'ID'   => 'actallrows_back-link',
						'TYPE' => 'CUSTOM',
						'VALUE' => '<button style="padding: 10px 20px;border-radius: 20px;"><a href="' . '/bitrix/admin/raketa_document_redactore.php?'.$linkParamsOut . '">Вернуться назад</a></button>',
					],
					[
						'ID'   => 'actallrows_back-link',
						'TYPE' => 'CUSTOM',
						'VALUE' => '<button style="padding: 10px 20px;border-radius: 20px;"><a href="' . '/bitrix/admin/raketa_document_editor.php?'.$linkParamsNew . '">Новый элеменит</a></button>',
					]
				]
			]
		]
	],
	'ALLOW_COLUMNS_SORT'        => false,
	'ALLOW_COLUMNS_RESIZE'      => true,
	'ALLOW_HORIZONTAL_SCROLL'   => true,
	'ALLOW_SORT'                => false,
	'ALLOW_PIN_HEADER'          => true,
	'AJAX_OPTION_HISTORY'       => 'N'
]);
?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>
