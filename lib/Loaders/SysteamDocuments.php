<?php

namespace Raketa\Plastfoil\Loaders;

use Raketa\Plastfoil\Helpers\IBlockElement;
use Bitrix\Main\Loader;
use Raketa\Plastfoil\Helpers\IBlockLID;
use \Raketa\Plastfoil\Table\RaketaDocumentsSectionsLinkTable;

class SysteamDocuments
{
	public static function recursiveTraversal($tree, $parentSectionID, $tags = [])
	{
		$sectionWorker = new \CIBlockSection;
		$iblockId = IBlockLID::IBlockIDByCode(IBlockLID::$IBLOCK_FILE_LIBRARY_GEREEAL);
		if (isset($tree['items'])) {
			$actualSection = $parentSectionID;
			if ($tree['name']) {
				$code = transliterate($tree['name']);
				$arLoadSectionArray = array(
					'ACTIVE' => 'Y',
					'IBLOCK_ID' => $iblockId,
					'NAME' => $tree['name'],
					'CODE' => $code,
					'SORT' => 500
				);
				if ($newSectionID = $sectionWorker->Add($arLoadSectionArray)) {
					RaketaDocumentsSectionsLinkTable::add([
						'IBLOCK_SECTION_PARENT_ID' => (int)$parentSectionID,
						'IBLOCK_SECTION_CHILD_ID' => (int)$newSectionID,
					]);
					$actualSection = $newSectionID;
					$tags = [strtolower($code)]; // eeee
				}
			}
			foreach ($tree['items']['list'] as $item) {
				self::recursiveTraversal($item, $actualSection, $tags);
			}
		} else if (is_array($tree)) {
			if (!empty($tree['name']) && $tree['href']) {
				$fileArray = \CFile::MakeFileArray('https://www.penoplex.ru' . $tree['href']);
				$fileArray['name'] = $tree['name'];
				$arLoadSectionArray = array(
					'ACTIVE' => 'Y',
					'IBLOCK_ID' => $iblockId,
					//'IBLOCK_SECTION_ID' => $parentSectionID,
					'NAME' => $tree['name'],
					'CODE' => transliterate($tree['name']),
					'SORT' => 500,
					'UF_FILE' => $fileArray,
					'UF_TAGS' => $tags,
				);
				if ($newSectionID = $sectionWorker->Add($arLoadSectionArray)) {
					RaketaDocumentsSectionsLinkTable::add([
						'IBLOCK_SECTION_PARENT_ID' => (int)$parentSectionID,
						'IBLOCK_SECTION_CHILD_ID' => (int)$newSectionID,
					]);
					dump('Успешно');
				} else {
					dump($sectionWorker->LAST_ERROR);
				}
			}
		}
	}

	public static function load()
	{
		$iblockElement = new \IBlockElement();
		$list = $iblockElement->setFilter([
			'IBLOCK_TYPE' => 'content',
			'IBLOCK_CODE' => 'system',
			'!=PROPERTY_LINK_PENOPLEX' => false
		])->setSelect([
			'IBLOCK_ID',
			'NAME',
			'PROPERTY_LINK_PENOPLEX',
			'CODE'
		])->getList();

		$iblockId = IBlockLID::IBlockIDByCode(IBlockLID::$IBLOCK_FILE_LIBRARY_GEREEAL);

		$systemSection = \CIBlockSection::GetList([], [
			'IBLOCK_ID' => $iblockId,
			'CODE' => 'system',
		], false, ['ID'])->fetch();

		if (!empty($systemSection)) {
			foreach ($list as $item) {
				$link = $item['PROPERTIES']['LINK_PENOPLEX']['VALUE'][0];

				$sectionWorker = new \CIBlockSection;

				$bdSectionQuery = \CIBlockSection::GetList([], [
					'IBLOCK_ID' => $iblockId,
					'IBLOCK_SECTION_ID' => $systemSection['ID'],
					'CODE' => $item['CODE']
				]);

				$section = $bdSectionQuery->Fetch();

				if (!empty($section)) {
					continue;
				}

				$arLoadSectionArray = array(
					'ACTIVE' => 'Y',
					'IBLOCK_ID' => $iblockId,
					'NAME' => $item['NAME'],
					'CODE' => $item['CODE'],
					'SORT' => 500
				);

				if ($newSectionID = $sectionWorker->Add($arLoadSectionArray)) {
					RaketaDocumentsSectionsLinkTable::add([
						'IBLOCK_SECTION_PARENT_ID' => (int)$systemSection['ID'],
						'IBLOCK_SECTION_CHILD_ID' => (int)$newSectionID,
					]);
					$html = file_get_contents($link);
					$dom = new \DOMDocument;
					$dom->loadHTML($html);
					$element = $dom->getElementById('documentation');
					$fileListJson = $element->getAttribute('data-documents');
					$fileTree = json_decode($fileListJson, true);
					recursiveTraversal($fileTree, $newSectionID);
					exit();
				}
			}
		}
	}
}
