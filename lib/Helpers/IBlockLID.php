<?php

namespace Wilp\Helpers;

class IBlockLID
{
	private static $baseIdIBlock = [];

	public static $IBLOCK_CATALOG = 'catalog';
	public static $IBLOCK_SYSTEM = 'system';
	public static $NEWS = 'news';

	public static $TEXT_PAGE = 'text_page';
	public static $IBLOCK_ABOUT_PAGE = 'about-setting';
	public static $IBLOCK_BIM_PAGE = 'bim-page';
	public static $IBLOCK_MAIN_PAGE = 'main-page';
	public static $IBLOCK_QUESTIONS_AND_ANSWERS = 'questions-and-answers';
	public static $IBLOCK_QUESTIONS_AND_ANSWERS_LIST = 'questions-and-answers-list';
	public static $IBLOCK_CONTACTS_PAGE = 'contacts-page';
	public static $IBLOCK_OBJECTS_PAGE = 'implemented-objects';
	public static $IBLOCK_VIDEO_MATERIAL = 'video-material';
	public static $IBLOCK_DOCUMENTS = 'document';

	public static function IBlockIDByCode($IBlockCode): string
	{
		if(!\Bitrix\Main\Loader::includeModule('iblock')) {
			die('module iblock not found');
		}

		if(empty(self::$baseIdIBlock)) {
			$currentSiteID = SITE_ID ?? 's2';
			$queryIBlockList = \CIBlock::GetList([ 'SORT' => 'DESC' ], [ 'LID' => $currentSiteID ]);

			while ($IBlock = $queryIBlockList->fetch()) {
				if(!isset(self::$baseIdIBlock[$IBlock['CODE']])) {
					self::$baseIdIBlock[$IBlock['CODE']] = $IBlock['ID'];
				}
			}
		}

		return self::$baseIdIBlock[$IBlockCode] ?? '0';
	}
}
