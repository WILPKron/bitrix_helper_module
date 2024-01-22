<?php

namespace Raketa\Plastfoil\Base;

use Raketa\Plastfoil\Helpers\Main as HelperMain;

class SetMetaData
{
	public static function setMeta($title, $description = '', $keyword = '', $navName = ''):void
	{
		if (!empty($title)) {
			HelperMain::getApp()->SetPageProperty('title', $title);
		}
		if(!empty($description)) {
			HelperMain::getApp()->SetPageProperty('description', $description);
		}
		if(!empty($keyword)) {
			HelperMain::getApp()->SetPageProperty('keywords', $keyword);
		}

		if(!empty($navName)) {
			self::setTitle($navName, 'Y');
		}
	}

	public static function setTitle($title, $addToNavChain = false)
	{
		HelperMain::getApp()->SetTitle($title);
		if($addToNavChain) {
			HelperMain::getApp()->AddChainItem($title);
		}
	}

	public static function element($IBLOCK_ID, $ELEMENT_ID, $ELEMENT_NAME, $NAV_CHAIN = 'Y')
	{
		global $APPLICATION;
		$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues(
			$IBLOCK_ID,
			$ELEMENT_ID
		);

		$seoDate = $ipropValues->getValues();

		if(!empty($seoDate)) {
			self::setMeta(
				$seoDate['ELEMENT_META_TITLE'],
				$seoDate['ELEMENT_META_DESCRIPTION'],
				$seoDate['ELEMENT_META_KEYWORDS']
			);
		}

		if(!empty($ELEMENT_NAME)) {
			self::setTitle($ELEMENT_NAME, $NAV_CHAIN === 'Y');
		}
	}

	public static function selction($IBLOCK_ID, $SECTION_ID, $SECTION_NAME, $NAV_CHAIN = 'Y', $NAV_CHAIN_PARENT = 'N')
	{
		global $APPLICATION;
		$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(
			$IBLOCK_ID,
			$SECTION_ID
		);

		$seoDate = $ipropValues->getValues();

		if(!empty($seoDate)) {
			if (!empty($seoDate['ELEMENT_META_TITLE'])) {
				$APPLICATION->SetPageProperty('title', $seoDate['ELEMENT_META_TITLE']);
			}
			if(!empty($seoDate['ELEMENT_META_DESCRIPTION'])) {
				$APPLICATION->SetPageProperty('description', $seoDate['ELEMENT_META_DESCRIPTION']);
			}
			if(!empty($seoDate['ELEMENT_META_KEYWORDS'])) {
				$APPLICATION->SetPageProperty('keywords', $seoDate['ELEMENT_META_KEYWORDS']);
			}
		}
		if($NAV_CHAIN === 'Y' && $NAV_CHAIN_PARENT === 'Y') {
			$dbParentSection = \CIBlockSection::GetNavChain($IBLOCK_ID, $SECTION_ID, array(), true);
			foreach ($dbParentSection as $section) {
				$url = \CIBlock::ReplaceDetailUrl($section['SECTION_PAGE_URL'], $section, true, 'S');
				$APPLICATION->AddChainItem($section['NAME'], $SECTION_ID !== $section['ID'] ? $url : '');
			}
		}

		if(!empty($SECTION_NAME)) {
			$APPLICATION->SetTitle($SECTION_NAME);
			if($NAV_CHAIN === 'Y' && $NAV_CHAIN_PARENT !== 'Y') {
				$APPLICATION->AddChainItem($SECTION_NAME);
			}
		}
	}
}
