<?php

namespace Raketa\Plastfoil\Loaders;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Raketa\Plastfoil\Table\WhereBuy\RaketaWhereBuyTable;
class WhereBuy
{
	public static function loader()
	{
		$spreadsheet = IOFactory::load(__DIR__ . '/../../../../../upload/loaders/whereBuy.xlsx');
		$sheet = $spreadsheet->getSheet(0);
		$whereBuy = $sheet->toArray();
		$list = [];
		foreach ($whereBuy as $key => $item) {
			if($key === 0) {
				continue;
			}
			$item[3] = explode(' и ', $item[3]);
			foreach ($item[3] as &$value) {
				$value = mb_ucfirst($value);
			}
			$item[13] = [$item[13]];
			if(!empty($item[14])) {
				$item[13][] = $item[14];
			}
			unset($item['14']);

			$item[17] = explode(';', $item[17]);

			foreach ($item[17] as &$value) {
				$value = trim($value);
			}

			$list[] = $item;
		}

		$inBase = RaketaWhereBuyTable::getList([
			'select' => ['UF_XML_ID', 'ID']
		])->fetchAll();

		$inBaseXmlID = array_map(fn($item) => $item['UF_XML_ID'], $inBase);

		$entity_data_class = RaketaWhereBuyTable::getIdHLEntity();

		foreach ($list as $item) {
			$key = array_search($item[0], $inBaseXmlID);
			$str = $item[9].','.$item[10];
			preg_match_all('/(-?\d{1,3}\.)\d{5,6}/', $str, $output_array);
			list($width, $longitude) = $output_array[0];
			$data = [
				'UF_XML_ID' => $item[0],
				'UF_NAME' => $item[1],
				'UF_PARTNER_STATUS' => $item[2],
				'UF_DIRECTION' => $item[3],
				'UF_COUNTRY' => $item[4],
				'UF_DISTRICT' => $item[5],
				'UF_REGION' => $item[6],
				'UF_CITY' => $item[7],
				'UF_ADDRESS' => $item[8],
				'UF_WIDTH' => $width,
				'UF_LONGITUDE' => $longitude,
				'UF_SITE' => $item[11],
				'UF_EMAIL' => $item[12],
				'UF_PHONE' => $item[13],
				'UF_SORT' => $item[15],
				'UF_PRODUCT_IDS' => $item[17]
			];
			if($key !== false) {
				$entity_data_class::update($inBase[$key]['ID'], $data);
			} else {
				$entity_data_class::add($data);
			}
		}
	}
}
