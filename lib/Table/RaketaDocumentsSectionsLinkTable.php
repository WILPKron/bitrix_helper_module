<?php

namespace Raketa\Plastfoil\Table;

use    Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\Relations\Reference,
	Raketa\Plastfoil\Model\SectionDocumentsTable,
	Bitrix\Main\ORM\Query\Join,
	Bitrix\Main\ORM\Event;

class RaketaDocumentsSectionsLinkTable extends RaketaDataManager
{

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'r_documents_sections_link';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			(new IntegerField('IBLOCK_SECTION_PARENT_ID'))->configurePrimary(true),
			(new IntegerField('IBLOCK_SECTION_CHILD_ID'))->configurePrimary(true),
			(new Reference(
				'PARENT',
				SectionDocumentsTable::class,
				Join::on('this.IBLOCK_SECTION_PARENT_ID', 'ref.ID'))
			)->configureJoinType('inner'),
			(new Reference(
				'CHILD',
				SectionDocumentsTable::class,
				Join::on('this.IBLOCK_SECTION_CHILD_ID', 'ref.ID'))
			)->configureJoinType('inner'),
		];
	}

	public static function sectionWhithoutParent()
	{
		$connection = \Bitrix\Main\Application::getConnection();
		$query = "
			SELECT DISTINCT IBLOCK_SECTION_PARENT_ID as ID FROM r_documents_sections_link
			WHERE IBLOCK_SECTION_PARENT_ID NOT IN (SELECT IBLOCK_SECTION_CHILD_ID FROM r_documents_sections_link)
		";
		$result = $connection->query($query);
		return $result->fetchAll();
	}

	public static function fetchChildInDataBase($parentId)
	{
		$connection = \Bitrix\Main\Application::getConnection();
		$linkTableName = self::getTableName();
		$query = "
			SELECT
				t1.IBLOCK_SECTION_CHILD_ID as level1,
				t2.IBLOCK_SECTION_CHILD_ID as level2,
				t3.IBLOCK_SECTION_CHILD_ID as level3,
				t4.IBLOCK_SECTION_CHILD_ID as level4,
				t5.IBLOCK_SECTION_CHILD_ID as level5,
				t6.IBLOCK_SECTION_CHILD_ID as level6
			FROM
			    $linkTableName AS t1
				LEFT JOIN $linkTableName AS t2 ON t2.IBLOCK_SECTION_PARENT_ID = t1.IBLOCK_SECTION_CHILD_ID
				LEFT JOIN $linkTableName AS t3 ON t3.IBLOCK_SECTION_PARENT_ID = t2.IBLOCK_SECTION_CHILD_ID
				LEFT JOIN $linkTableName AS t4 ON t4.IBLOCK_SECTION_PARENT_ID = t3.IBLOCK_SECTION_CHILD_ID
				LEFT JOIN $linkTableName AS t5 ON t5.IBLOCK_SECTION_PARENT_ID = t4.IBLOCK_SECTION_CHILD_ID
				LEFT JOIN $linkTableName AS t6 ON t6.IBLOCK_SECTION_PARENT_ID = t5.IBLOCK_SECTION_CHILD_ID
			WHERE ";
		$queryWhere = '';
		if (is_array($parentId)) {
			foreach ($parentId as $id) {
				if (empty($queryWhere)) {
					$queryWhere .= 't1.IBLOCK_SECTION_PARENT_ID = ' . $id;
				} else {
					$queryWhere .= ' OR t1.IBLOCK_SECTION_PARENT_ID = ' . $id;
				}
			}
			$query .= $queryWhere;
		} else {
			$query .= 't1.IBLOCK_SECTION_PARENT_ID = ' . $parentId;
		}

		$result = $connection->query($query);
		if (is_array($parentId)) {
			$list = $parentId;
		} else {
			$list = [$parentId];
		}

		foreach ($result->fetchAll() as $item) {
			foreach ($item as $value) {
				$list[] = $value;
			}
		}
		return array_unique(array_filter($list));
	}

	/**
	 * Удаление записи
	 * @param array $primary ['parent' => number, 'child' => number] массив пара ключ значение для удаление записи
	 * @throws \Exception
	 */
	public static function delete($primary)
	{
		$parent = (int)$primary['parent'];
		$child = (int)$primary['child'];

		if (empty($parent) || empty($child)) {
			return false;
		}

		$connection = \Bitrix\Main\Application::getConnection();
		$query = "DELETE FROM " . self::getTableName() . " WHERE IBLOCK_SECTION_CHILD_ID = " . $child . " AND IBLOCK_SECTION_PARENT_ID = " . $parent;
		$connection->queryExecute($query);

		$res = self::getList([
			'filter' => [
				'IBLOCK_SECTION_CHILD_ID' => $child,
				'IBLOCK_SECTION_PARENT_ID' => $parent,
			]
		])->fetch();

		return empty($res);
	}

	public static function deleteAll($id)
	{
		$id = (int)$id;
		if (empty($id)) {
			return false;
		}
		$connection = \Bitrix\Main\Application::getConnection();
		$query = "DELETE FROM " . self::getTableName() . " WHERE IBLOCK_SECTION_CHILD_ID = " . $id . " OR IBLOCK_SECTION_PARENT_ID = " . $id;
		$connection->queryExecute($query);
	}
}
