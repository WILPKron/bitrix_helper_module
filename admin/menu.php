<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

// if ($APPLICATION->GetGroupRight('') != 'D') {
	$aMenu = [
		'parent_menu' => 'global_menu_content',
		'section' => 'raketa',
		'sort' => 50,
		'text' => "raketa.agency",
		'title' => "raketa.agency",
		'icon' => 'fav_menu_icon_yellow',
		'page_icon' => 'fav_menu_icon_yellow',
		'items_id' => 'menu_subscribe',
		'items' => [
			[
				'text' => "Параметры модуля",
				'url' => 'settings.php?lang=ru&mid=raketa.plastfoil',
				'icon' => 'util_menu_icon',
				'more_url' => [
					'posting_edit.php'
				],
				'title' => GetMessage('mnu_posting_alt')
			],
			[
				'text' => "Одностарничные инфоблоки",
				'url' => 'raketa_oneblockelement.php',
				'icon' => 'rating_menu_icon',
				'more_url' => [
					'posting_edit.php'
				],
				'title' => GetMessage('mnu_posting_alt')
			],
			[
				'text' => "Конструктор составного поля",
				'url' => 'multiple_type_creator.php',
				'icon' => 'util_menu_icon',
				'more_url' => [
					'posting_edit.php'
				],
				'title' => GetMessage('mnu_posting_alt')
			],
			[
				'text' => "Документы",
				'url' => 'raketa_document_redactore.php',
				'icon' => 'util_menu_icon',
				'more_url' => [
					'posting_edit.php'
				],
				'title' => GetMessage('mnu_posting_alt')
			],
			[
				'text' => 'Конструктор меню',
				'url' => 'raketa_menu_editor.php',
				'icon' => 'util_menu_icon',
			]
		]
	];

	return $aMenu;
//}
