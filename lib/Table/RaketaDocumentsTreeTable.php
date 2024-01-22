<?php

namespace Raketa\Plastfoil\Table;

use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Query\Join;
class RaketaDocumentsTreeTable extends RaketaDataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'r_documents_tree';
	}
	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => 'ID'
				]
			),
			new TextField(
				'NAME',
				[ 'title' => 'Имя' ]
			),
			new TextField(
				'CODE',
				[ 'title' => 'Символьный код' ]
			),
			new BooleanField(
				'ACTIVE',
				[
					'values' => array('N', 'Y'),
					'default' => 'Y',
					'title' => 'Активность'
				]
			),
			new IntegerField(
				'UF_FILE',
				[
					'title' => 'Файл',
					'save_data_modification' => fn () => [
						fn ($file) => \CFile::SaveFile($file, 'raketa')
					],
					'fetch_data_modification' => fn () => [
						fn ($fileId) => \CFile::GetFileArray($fileId)
					]
				]
			),
			new IntegerField(
				'SORT',
				[ 'title' => 'Сортировка' ]
			),

			// new TextField('UF_TAGS', [
			// 	'serialized' => true,
			// 	'title' => 'Тэги'
			// ]),

			(new Reference(
				'UF_TAGS',
				\Raketa\Plastfoil\Table\RaketaDocumentsTags::class,
				Join::on('this.ID', 'ref.SECTION_ID')
			))->configureJoinType('inner'),

			// new TextField('UF_TAGS', [
			// 	'save_data_modification' => function () {
			// 		return [
			// 			function ($array) {
			// 				return serialize($array);
			// 			}
			// 		];
			// 	},
			// 	'fetch_data_modification' => fn () => [
			// 		function ($array) {
			// 			return unserialize($array);
			// 		}
			// 	]
			// ]),

			(new ManyToMany('CHILD_SECTIONS', self::class))
				->configureTableName(RaketaDocumentsSectionsLinkTable::getTableName())
				->configureLocalPrimary('ID', 'IBLOCK_SECTION_PARENT_ID')
				->configureLocalReference('PARENT')
				->configureRemotePrimary('ID', 'IBLOCK_SECTION_CHILD_ID')
				->configureRemoteReference('CHILD'),

			(new ManyToMany('PARENT_SECTIONS', self::class))
				->configureTableName(RaketaDocumentsSectionsLinkTable::getTableName())
				->configureLocalPrimary('ID', 'IBLOCK_SECTION_CHILD_ID')
				->configureLocalReference('CHILD')
				->configureRemotePrimary('ID', 'IBLOCK_SECTION_PARENT_ID')
				->configureRemoteReference('PARENT'),
		];
	}
	public static function sectionTreeBuilder($sectionTree, $selectSectionID = false)
	{
		if($selectSectionID === false) {
			$res = [];
			foreach ($sectionTree as $section) {
				if(!empty($section['CHILD'])) {
					$res[$section['ID']] = self::sectionTreeBuilder($sectionTree, $section['ID']);
				}
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
			} else {
				unset($res['CHILD']);
			}
		}

		return $res;
	}

	public static function getListTree($parent = false, array $select = [])
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
			'filter' => [ 'UF_FILE' => false ]
		];

		if(!empty($parent)) {
			$parameters['filter']['ID'] = RaketaDocumentsSectionsLinkTable::fetchChildInDataBase($parent);
		}

		$documentsQuery = self::getList($parameters);



		$sectionTree = [];
		while($section = $documentsQuery->fetch()) {
			if(!isset($sectionTree[$section['ID']])) {
				if(!empty($section['CHILD'])) {
					$section['CHILD'] = [ $section['CHILD'] ];
				}
				$sectionTree[$section['ID']] = $section;
			} else {
				if(!empty($section['CHILD']) && !empty($sectionTree[$section['ID']]['CHILD']) && !in_array($section['CHILD'], $sectionTree[$section['ID']]['CHILD'])) {
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
}
