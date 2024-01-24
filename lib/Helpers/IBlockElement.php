<?php

namespace Wilp\Helpers;

use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Application;
use Bitrix\Main\Context;

class IBlockElement
{
	/** @var array $order массив для сортировки элементов **/
	public $order = ['SORT' => 'ASC'];
	/** @var array $filter массив фильрации элементов **/
	public $filter = [];
	/** @var array|boolean $group массив для группировки элементов **/
	public $group = false;
	/** @var array|boolean $nav массив для настройки навигации элементов **/
	public $nav = false;
	/** @var array $select массив полей которые будут выбраны у элемента **/
	public $select = ['ID', 'IBLOCK_ID'];
	/** @var boolean $getDescription поле для выборки пользовательских свойст элемента **/
	public $getDescription = false;
	public $propertyList = [];
	public $iblockIDs = [];
	private $navData = null;

	public $cache = NULL;
	public $taggedCache = NULL;
	public $cacheKey = NUll;
	public $cachePath = 'raketa-cache-iblock-element';
	public $cacheTtl = false;

	public function __construct($cacheTtl = (86400 * 30))
	{
		if(!\Bitrix\Main\Loader::includeModule("iblock")) {
			throw new \Exception('Модуль инфоблока не найден');
		}
		$this->cacheTtl = $cacheTtl;
		$this->cache = Cache::createInstance();
		$this->taggedCache = Application::getInstance()->getTaggedCache();
	}

	public function setPageNumberKey($key)
	{
		$this->pageNumberKey = $key;
	}

	public function getQuery()
	{
		return \CIBlockElement::GetList(
			$this->order,
			$this->filter,
			$this->group,
			$this->nav,
			$this->select
		);
	}

	private function getData($db)
	{
		$this->iblockIDs = [];

		$listPageUrl = [];

		while ($item = $db->fetch()) {
			if(!empty($item['DETAIL_PAGE_URL'])) {
				$item['DETAIL_PAGE_URL'] = \CIBlock::ReplaceDetailUrl($item['DETAIL_PAGE_URL'], $item, true, 'E');
			}
			if(!empty($item['LIST_PAGE_URL'])) {
				if(empty($listPageUrl[$item['IBLOCK_ID']])) {
					$item['LIST_PAGE_URL'] = \CIBlock::ReplaceDetailUrl($item['LIST_PAGE_URL'], $item, true, false);
					$listPageUrl[$item['IBLOCK_ID']] = $item['LIST_PAGE_URL'];
				} else {
					$item['LIST_PAGE_URL'] = $listPageUrl[$item['IBLOCK_ID']];
				}

			}

			if (empty($res[$item['ID']])) {
				$res[$item['ID']] = $item;
				$res[$item['ID']]['PROPERTIES'] = [];
			}

			if (!in_array($item['IBLOCK_ID'], $this->iblockIDs)) {
				$this->iblockIDs[$item['IBLOCK_ID']][] = $item['ID'];
			}

			if ($this->getDescription === false && !empty($this->propertyList)) {
				foreach ($this->propertyList as $prop) {
					if (empty($res[$item['ID']]['PROPERTIES'][$prop])) {
						$res[$item['ID']]['PROPERTIES'][$prop] = [];
						$res[$item['ID']]['PROPERTIES'][$prop]['VALUE'] = [];
					}
					if (!empty($item["PROPERTY_{$prop}_VALUE"])) {
						$res[$item['ID']]['PROPERTIES'][$prop]['VALUE'][$item["PROPERTY_{$prop}_VALUE_ID"]] = $item["PROPERTY_{$prop}_VALUE"];
					}
				}
			}
		}
		if (!empty($res)  && !empty($this->propertyList)) {
			if ($this->getDescription) {
				foreach ($res as &$item) {
					$dbListVacanciesProps = \CIBlockElement::GetProperty(
						$item['IBLOCK_ID'],
						$item['ID'],
						"sort",
						"asc",
						[]
					);
					while ($vacanciesProps = $dbListVacanciesProps->GetNext()) {
						if (empty($item['PROPERTIES'][$vacanciesProps['CODE']])) {
							$item['PROPERTIES'][$vacanciesProps['CODE']] = [];
						}
						// $item['PROPERTIES'][$vacanciesProps['CODE']]['INFO'] = $vacanciesProps;
						$item['PROPERTIES'][$vacanciesProps['CODE']]['MULTIPLE'] = $vacanciesProps['MULTIPLE'];
						$item['PROPERTIES'][$vacanciesProps['CODE']]['NAME'] = $vacanciesProps['NAME'];
						if ($vacanciesProps['MULTIPLE'] === 'Y') {
							$item['PROPERTIES'][$vacanciesProps['CODE']]['VALUE'][] = $vacanciesProps['~VALUE'];
							$item['PROPERTIES'][$vacanciesProps['CODE']]['DESCRIPTION'][] = $vacanciesProps['DESCRIPTION'];
						} else {
							$item['PROPERTIES'][$vacanciesProps['CODE']]['VALUE'] = $vacanciesProps['~VALUE'];
							$item['PROPERTIES'][$vacanciesProps['CODE']]['DESCRIPTION'] = $vacanciesProps['DESCRIPTION'];
						}
					}
				}


			} else {
				foreach ($res as &$item) {
					foreach ($this->propertyList as $prop) {
						unset($item["PROPERTY_{$prop}_VALUE_ID"]);
						unset($item["PROPERTY_{$prop}_VALUE"]);
						if (!empty($item['PROPERTIES'][$prop]['VALUE'])) {
							$item['PROPERTIES'][$prop]['VALUE'] = array_values($item['PROPERTIES'][$prop]['VALUE']);
						}
					}
				}
			}
		}
		return !empty($res) ? array_values($res) : [];
	}

	public function getNav()
	{
		return $this->navData;
	}

	public function getList()
	{
		$db = $this->getQuery();
		$res = [];
		$page = false;

		if(!empty($this->nav)) {
			$this->navData = $db->GetPageNavStringEx($navComponentObject, $this->nav['title'], $this->nav['template']);
		}

		$context = Context::getCurrent();
		$language = $context->getLanguage() ?? 'ru';

		if($this->cacheTtl !== false) {

			$cacheKeyArray = [
				$this->order,
				$this->filter,
				$this->group,
				$this->nav,
				$this->select,
				$this->getDescription,
				$language
			];

			if(!empty($page)) {
				$cacheKeyArray[] = $page;
			}

			$this->cacheKey = serialize($cacheKeyArray);
			if ($this->cache->initCache($this->cacheTtl, $this->cacheKey, $this->cachePath)) {
				$res = $this->cache->getVars();
			} elseif ($this->cache->startDataCache()) {
				$this->taggedCache->startTagCache($this->cachePath);
				$res = $this->getData($db);
				if(!empty($res)) {
					foreach (array_keys($this->iblockIDs) as $iblockID) {
						$this->taggedCache->registerTag('iblock_id_' . $iblockID);
					}
				} else {
					$this->taggedCache->abortTagCache();
					$this->cache->abortDataCache();
				}

				$this->taggedCache->endTagCache();
				$this->cache->endDataCache($res);
			}
		} else {
			$res = $this->getData($db);
		}
		return $res;
	}

	public function setGetDescription($on)
	{
		$this->getDescription = $on;
		return $this;
	}

	public function setSelect($select)
	{

		$this->propertyList = [];
		foreach ($select as $key => $item) {
			if(strpos($item, 'PROPERTY_') === 0) {
				$this->propertyList[] = str_replace('PROPERTY_', '', $item);
				if($this->getDescription !== false) {
					unset($select[$key]);
				}
			}
		}
		if(!empty($this->propertyList)) {
			if(!in_array('ID', $select)) {
				$select[] = 'ID';
			}
			if(!in_array('IBLOCK_ID', $select)) {
				$select[] = 'IBLOCK_ID';
			}
		}
		$this->select = $select;
		return $this;
	}

	public function setNav($nav)
	{
		if(!isset($nav['iNumPage'])) {
			global $NavNum;
			if(!isset($NavNum) && !isset($nav['key'])) {
				$NavNum = 1;
			}
			$nav['key'] = $nav['key'] ?? 'PAGEN_' . $NavNum;
			$request = Application::getInstance()->getContext()->getRequest();
			$page = $request->getQuery($nav['key']) ?? 1;
			if(str_contains($nav['key'], 'PAGEN_')) {
				$NavNum = explode('_', $nav['key'])[1] - 1;
			}
			$nav['iNumPage'] = $page;
		}

		if(empty($nav['template'])) {
			$nav['template'] = "number_navigation";
		}
		if(empty($nav['title'])) {
			$nav['title'] = "Страницы: ";
		}
		$this->nav = $nav;
		return $this;
	}

	public function setGroup($group)
	{
		$this->group = $group;
		return $this;
	}

	public function setFilter($filter)
	{
		$this->filter = $filter;
		return $this;
	}

	public function setOrder($order)
	{
		$this->order = $order;
		return $this;
	}

	public function first()
	{
		$res = $this->getList();
		return array_shift($res);
	}

}
