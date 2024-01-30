<?php

namespace Wilp\Helpers;

use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Application;
use Bitrix\Main\Context;
class IBlockSection
{

	public $order = ['SORT' => 'ASC'];
	public $filter = [];
	public $bIncCnt = false;
	public $nav = false;
	public $select = ['ID', 'IBLOCK_ID'];
	public $getTree = false;

	public $cache = NULL;
	public $taggedCache = NULL;
	public $cacheKey = NUll;
	public $cachePath = 'wilp-cache-iblock-section';
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

	public function getQuery()
	{
		return \CIBlockSection::GetList(
			$this->order,
			$this->filter,
			$this->bIncCnt,
			$this->select,
			$this->nav
		);
	}

	public function setSelect($select)
	{
		if(!in_array('SECTION_PAGE_URL', $select)) {
			$select[] = 'SECTION_PAGE_URL';
		}
		if(!in_array('LIST_PAGE_URL', $select)) {
			$select[] = 'LIST_PAGE_URL';
		}
		$this->select = $select;
		return $this;
	}

	public function setNav($nav)
	{
		$this->nav = $nav;
		return $this;
	}

	public function setbIncCnt($bIncCnt)
	{
		$this->bIncCnt = !!$bIncCnt;
		return $this;
	}

	public function setFilter($filter)
	{
		if(empty($filter['GLOBAL_ACTIVE'])) {
			$filter['GLOBAL_ACTIVE'] = 'Y';
		}

		$this->filter = $filter;
		return $this;
	}

	public function setOrder($order)
	{
		$this->order = $order;
		return $this;
	}

	private function getData($db)
	{
		$this->iblockIDs = [];

		while ($item = $db->fetch()) {
			if(!empty($item['SECTION_PAGE_URL'])) {
				$item['SECTION_PAGE_URL'] = \CIBlock::ReplaceSectionUrl($item['SECTION_PAGE_URL'], $item, true, 'S');
			}
			if(!empty($item['LIST_PAGE_URL'])) {
				$item['LIST_PAGE_URL'] = \CIBlock::ReplaceSectionUrl($item['LIST_PAGE_URL'], $item, true, false);
			}
			if (!in_array($item['IBLOCK_ID'], $this->iblockIDs)) {
				$this->iblockIDs[$item['IBLOCK_ID']][] = $item['ID'];
			}
			dump($item);
		}
		return !empty($res) ? array_values($res) : [];
	}

	public function getList()
	{
		$db = $this->getQuery();
		$res = [];
		$page = false;

		// if(!empty($this->nav)) {
		// 	$this->navData = $db->GetPageNavStringEx($navComponentObject, $this->nav['title'], $this->nav['template']);
		// }

		$context = Context::getCurrent();
		$language = $context->getLanguage() ?? 'ru';

		if($this->cacheTtl !== false) {

			$cacheKeyArray = [
				$this->order,
				$this->filter,
				$this->bIncCnt,
				$this->select,
				$this->nav,
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

	public function first()
	{
		$res = $this->getList();
		return array_shift($res);
	}

}
