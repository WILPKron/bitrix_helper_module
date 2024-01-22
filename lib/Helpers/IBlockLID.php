<?php

namespace Raketa\Plastfoil\Helpers;

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
	public static $IBLOCK_FILE_LIBRARY_GEREEAL = 'file-library-general';
	public static $IBLOCK_SERVICES = 'services';

	/**
	 * Функция получения инфоблока по его коду. При первом запросе данные сохраняются в статическую переменную
	 * для дальнейшьего использования.
	 *
	 * Функция используется для определения инфоблока в компоненте в разныех языковых версиях.
	 * @param string $IBlockCode символьный код инфоблока
	 * @return array массив данных инфоблока
	**/

	public static function IBlockIDByCode($IBlockCode): string
	{
		if(!\Bitrix\Main\Loader::includeModule('iblock')) {
			die('module iblock not found');
		}
		if(empty(self::$baseIdIBlock)) {
			$currentSiteID = SITE_ID ?? 's2';
			if($currentSiteID === 'ru') {
				$currentSiteID = 's1';
			}
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
