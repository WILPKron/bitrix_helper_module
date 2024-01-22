<?php

namespace Raketa\Plastfoil\Services;

use Raketa\Plastfoil\Helpers\IBlockElement;
use Raketa\Plastfoil\Helpers\IBlockLID;
use Bitrix\Main\UI\PageNavigation as PageNavigationAlias;
use Raketa\Plastfoil\Helpers\Main;
use \Raketa\Plastfoil\Table\WhereBuy\RaketaWhereBuyTable;
use Bitrix\Main\Application;
class WhereBuyService
{
	public function getData()
	{
		$data = [];

		$filter = [];

		$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

		if(!empty($request->get('UF_PARTNER_STATUS'))) {
			$filter['UF_PARTNER_STATUS'] = $request->get('UF_PARTNER_STATUS');
		}
		if(!empty($request->get('UF_COUNTRY'))) {
			$filter['UF_COUNTRY'] = $request->get('UF_COUNTRY');
		}
		if(!empty($request->get('UF_CITY'))) {
			$filter['UF_CITY'] = $request->get('UF_CITY');
		}
		if(!empty($request->get('UF_PRODUCT_IDS'))) {
			$filter['UF_PRODUCT_IDS'] = $request->get('UF_PRODUCT_IDS');
		}

		$entitySuppliers = RaketaWhereBuyTable::getIdHLEntity();
		$suppliers = $entitySuppliers::getList([
			'filter' => $filter
		])->fetchAll();

		$partnerStatus = [];
		$countries = [];
		$cities = [];
		$productIds = [];

		foreach ($suppliers as $supplier) {
			$partnerStatus[] = $supplier['UF_PARTNER_STATUS'];
			$countries[] = $supplier['UF_COUNTRY'];
			$cities[] = $supplier['UF_CITY'];
			array_push($productIds, ...$supplier['UF_PRODUCT_IDS']);
		}

		$data['PARTNER_STATUS'] = array_unique($partnerStatus);
		$data['COUNTRIES'] = array_unique($countries);
		$data['CITIES'] = array_unique($cities);
		$productIds = array_unique($productIds);

		$elementWorker = new IBlockElement();

		if($productIds) {
			$products = $elementWorker->setSelect(['CODE', 'ID', 'NAME'])->setFilter([
				'IBLOCK_ID' => IBlockLID::IBlockIDByCode(IBlockLID::$IBLOCK_CATALOG),
				'CODE' => $productIds
			])->getList();
		} else {
			$products = [];
		}


		$data['SUPPLIERS_JSON'] = json_encode(array_map(fn($item) => [
			'type' => 'site',
			'coords' => [ $item['UF_WIDTH'], $item['UF_LONGITUDE'] ],
			'details' => [
				'balloonContent' => '<strong>'. $item['UF_NAME'] .' в '. $item['UF_CITY'] .'</strong><br>'. $item['UF_REGION'] .', ' . $item['UF_ADDRESS'],
				'iconContent' => $item['UF_CITY']
			]
		], $suppliers),JSON_UNESCAPED_UNICODE);

		$data['PRODUCTS_FILTER'] = $products;

		$data['PRODUCTS'] = [];

		foreach ($products as $product) {
			$data['PRODUCTS'][$product['CODE']] = $product;
		}


		$nav = new PageNavigationAlias('number_navigation');
		$nav->allowAllRecords(true)->setPageSize(5)->initFromUri();

		$suppliersQuery = $entitySuppliers::getList([
			'filter' => $filter,
			"count_total" => true,
			'limit' => $nav->getLimit(),
			'offset' => $nav->getOffset()
		]);

		$data['ROWS'] = $suppliersQuery->fetchAll();

		foreach ($data['ROWS'] as &$row) {
			foreach ($row['UF_PRODUCT_IDS'] as $key => $PRODUCT_ID) {
				if($data['PRODUCTS'][$PRODUCT_ID]['NAME']) {
					$row['UF_PRODUCT'][$PRODUCT_ID] = $data['PRODUCTS'][$PRODUCT_ID];
				} else {
					unset($row['UF_PRODUCT_NAME'][$key]);
				}
			}
		}

		$nav->setRecordCount($suppliersQuery->getCount());

		ob_start();
		Main::getApp()->IncludeComponent(
			"bitrix:main.pagenavigation",
			'',
			array(
				"NAV_OBJECT" => $nav,
			),
			false
		);
		$data['NAV'] = ob_get_clean();

		return $data;
	}
}
