<?php

namespace Raketa\Plastfoil\Table;

use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;

class RaketaDocumentsTagsTable extends RaketaDataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'r_documents_tags';
	}
	public static function getMap()
	{
		return [
			new IntegerField('ID', ['primary' => true, 'autocomplete' => true, 'title' => 'ID']),
			new IntegerField('SECTION_ID'),
			new TextField('VALUE', [ 'title' => 'Значение' ])
		];
	}
}
