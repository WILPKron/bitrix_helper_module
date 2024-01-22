<?php

namespace Raketa\Plastfoil\Table\WhereBuy;

use Bitrix\Main\Entity\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Query\Join;
use Raketa\Plastfoil\Table\RaketaDataManager;

class RaketaWhereBuyTable extends RaketaDataManager
{

	public static function getTableName()
	{
		return 'r_where_buy';
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
			new BooleanField('UF_MAIN_BUILDING', [
				'values' => array('N', 'Y'),
				'default' => 'N',
				'title' => 'Главное здание',
			]),
			new TextField('UF_XML_ID', [
				'title' => 'Внешний ключ',
			]),
			new TextField('UF_NAME', [
				'title' => 'Имя',
				'required' => true,
			]),
			new TextField('UF_PARTNER_STATUS', [
				'title' => 'Статус партнера',
				'required' => true,
			]),
			new TextField('UF_DIRECTION', [
				'title' => 'Направление деятельности',
				'serialized' => true,
			]),
			new TextField('UF_COUNTRY', [
				'title' => 'Страна',
				'required' => true,
			]),
			new TextField('UF_DISTRICT', [
				'title' => 'Округ'
			]),
			new TextField('UF_REGION', [
				'title' => 'Регион'
			]),
			new TextField('UF_CITY', [
				'title' => 'Город',
				'required' => true,
			]),
			new TextField('UF_ADDRESS', [
				'title' => 'Адрес',
				'required' => true,
			]),
			new TextField('UF_WIDTH', [
				'title' => 'Широта',
				'required' => true,
			]),
			new TextField('UF_LONGITUDE', [
				'title' => 'Долгота',
				'required' => true,
			]),
			new TextField('UF_SITE', [
				'title' => 'Сайт'
			]),
			new TextField('UF_EMAIL', [
				'title' => 'Почта'
			]),
			new TextField('UF_PHONE', [
				'title' => 'Телефоны',
				'required' => true,
				'serialized' => true,
			]),
			new IntegerField('UF_SORT', [
				'title' => 'Сортировка'
			]),
			new TextField('UF_PRODUCT_IDS',[
				'title' => 'Продукты',
				'serialized' => true,
			]),
		];
	}
}
