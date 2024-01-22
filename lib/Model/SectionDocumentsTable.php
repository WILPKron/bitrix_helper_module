<?php

namespace Raketa\Plastfoil\Model;

use Bitrix\Landing\Node\Embed;
use Bitrix\Main\ArgumentException;
use Raketa\Plastfoil\Helpers\IBlockLID;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Entity;
use \Raketa\Plastfoil\Table\RaketaDocumentsSectionsLinkTable;
class SectionDocumentsTable extends SectionTable
{
	public static function getUfId()
	{
		$iblockId = IBlockLID::IBlockIDByCode(IBlockLID::$IBLOCK_FILE_LIBRARY_GEREEAL);
		return "IBLOCK_" . $iblockId . "_SECTION";
	}

	/**
	 * @throws ArgumentException
	 */
	public static function getMap()
	{

		$fields = parent::getMap();

		$fields["PARENT_SECTION"] = [
			"data_type" => self::class,
			"reference" => ["=this.IBLOCK_SECTION_ID" => "ref.ID"],
		];

		unset($fields['LEFT_MARGIN']);
		unset($fields['RIGHT_MARGIN']);
		unset($fields['DEPTH_LEVEL']);
		unset($fields['SOCNET_GROUP_ID']);

		$fields[] = (new ManyToMany('CHILD_SECTIONS', self::class))
			->configureTableName(RaketaDocumentsSectionsLinkTable::getTableName())
			->configureLocalPrimary('ID', 'IBLOCK_SECTION_PARENT_ID')
			->configureLocalReference('PARENT')
			->configureRemotePrimary('ID', 'IBLOCK_SECTION_CHILD_ID')
			->configureRemoteReference('CHILD');

		$fields[] = (new ManyToMany('PARENT_SECTIONS', self::class))
			->configureTableName(RaketaDocumentsSectionsLinkTable::getTableName())
			->configureLocalPrimary('ID', 'IBLOCK_SECTION_CHILD_ID')
			->configureLocalReference('CHILD')
			->configureRemotePrimary('ID', 'IBLOCK_SECTION_PARENT_ID')
			->configureRemoteReference('PARENT');

		return $fields;
	}

	public static function sectionTreeBuilder($sectionTree, $selectSectionID = false)
	{
		if($selectSectionID === false) {
			$res = [];
			foreach ($sectionTree as $section) {
				//if(!empty($section['CHILD'])) {
					$res[$section['ID']] = self::sectionTreeBuilder($sectionTree, $section['ID']);
				//}
			}
		} else {
			if(!isset($sectionTree[$selectSectionID])) {
				return null;
			}
			$res = $sectionTree[$selectSectionID];
			if(!empty($res['CHILD'])) {
				foreach ($res['CHILD'] as $key => $childID) {
					$res['CHILD'][$key] = self::sectionTreeBuilder($sectionTree, $childID);
				}
			}
		}

		return $res;
	}

	public static function getListTree($parent = false, array $select = [], $selectFile = false)
	{
		if($parent === false) {
			$parent = RaketaDocumentsSectionsLinkTable::sectionWhithoutParent();
			$parent = array_map(fn($item) => $item['ID'], $parent);
		}

		if(!in_array('ID', $select)) {
			$select[] = 'ID';
		}
		if(!in_array('CHILD', $select)) {
			$select['CHILD'] = 'CHILD_SECTIONS.ID';
		}
		$parameters = [
			'select' => $select,
			'filter' => []
		];

		if($selectFile === false) {
			$parameters['filter']['UF_FILE'] = false;
		}

		if(!empty($parent)) {
			$parameters['filter']['ID'] = RaketaDocumentsSectionsLinkTable::fetchChildInDataBase($parent);
		}

		$documentsQuery = self::getList($parameters);



		$sectionTree = [];
		while($section = $documentsQuery->fetch()) {
			if(!empty($section['UF_FILE'])) {
				$section['UF_FILE'] = \CFile::GetFileArray($section['UF_FILE']);
			}
			if(!isset($sectionTree[$section['ID']])) {
				if(!empty($section['CHILD'])) {
					$section['CHILD'] = [ $section['CHILD'] ];
				} else {
					$section['CHILD'] = [  ];
				}

				$sectionTree[$section['ID']] = $section;
			} else {
				if(!empty($section['CHILD']) && !in_array($section['CHILD'], $sectionTree[$section['ID']]['CHILD'])) {
					$sectionTree[$section['ID']]['CHILD'][] = $section['CHILD'];
				}
			}
		}

		if(is_array($parent)) {
			$tree = self::sectionTreeBuilder($sectionTree);

			$treeResult = [];
			foreach ($parent as $id) {
				$treeResult[] = $tree[$id];
			}
			return $treeResult;
		} else {
			return self::sectionTreeBuilder($sectionTree, $parent);
		}

	}

	public static function setDefaultScope($query)
	{
		$iblockId = IBlockLID::IBlockIDByCode(IBlockLID::$IBLOCK_FILE_LIBRARY_GEREEAL);
		return $query->where("IBLOCK_ID", $iblockId);
	}
}
