<?php

namespace Raketa\Plastfoil\Loaders;

use Bitrix\Main\Loader;
use Raketa\Plastfoil\Helpers\IBlockLID;
use PhpOffice\PhpSpreadsheet\IOFactory;
class News
{
	public static function loader()
	{
		if(Loader::includeModule('iblock')) {

			$spreadsheet = IOFactory::load(__DIR__ . '/../../../../upload/newLoader.xlsx');
			$sheet = $spreadsheet->getSheet(0);
			$news = $sheet->toArray();

			foreach ($news as $key => $newsLine) {
				if ($key > 4) {
					if (
						empty($newsLine[0]) ||
						empty($newsLine[1]) ||
						empty($newsLine[4]) ||
						empty($newsLine[3])
					) {
						continue;
					}
					$elementObject = new \CIBlockElement();
					$name = trim($newsLine[1]);
					$nameCode = transliterate($name);
					$nameCode = preg_replace('/[^a-zA-Zа-яА-Я0-9-]/ui', '', $nameCode);
					$nameCode = strtolower($nameCode);

					$elementWorker = new \IBlockElement();

					$element = $elementWorker->setFilter([
						'IBLOCK_ID' => IBlockLID::IBlockIDByCode(IBlockLID::$NEWS),
						'CODE' => $nameCode,
					])->setSelect(['ID'])->first();

					if(!empty($element)) {
						continue;
					}

					$elementFields = [
						'IBLOCK_ID' => IBlockLID::IBlockIDByCode(IBlockLID::$NEWS),
						'NAME' => $name,
						'CODE' => $nameCode,
						'PREVIEW_TEXT' => trim($newsLine[2]),
						'PREVIEW_TEXT_TYPE' => 'html',
						'DETAIL_TEXT_TYPE' => 'html',
						'XML_ID' => $newsLine[0],
						'DATE_ACTIVE_FROM' => ConvertTimeStamp(strtotime($newsLine[4]))
					];

					$newsLine[3] = str_replace('_x000D_', '', $newsLine[3]);
					$pattern = '/src="(.*?)"/i';

					$resultPregMatch = [];
					preg_match($pattern, $newsLine[3], $resultPregMatch);

					if (!empty($resultPregMatch) && !empty($resultPregMatch[1])) {
						$innerTextImageLink = 'https://plastfoil.ru' . $resultPregMatch[1];
						$elementFields['DETAIL_PICTURE'] = \CFile::MakeFileArray($innerTextImageLink);
						$newsLine[3] = preg_replace('/<img.*?(.*?) .*\/>/i', '', $newsLine[3]);
						$newsLine[3] = preg_replace('/<p>\s*<\/p>/', '', $newsLine[3]);
					}
					sleep(2);
					$elementFields['DETAIL_TEXT'] = $newsLine[3];

					if (!empty($newsLine[5])) {
						$elementFields['PREVIEW_PICTURE'] = \CFile::MakeFileArray($newsLine[5]);
					}

					if ($PRODUCT_ID = $elementObject->Add($elementFields)) {
						dump("New ID: " . $PRODUCT_ID);
					}
					sleep(2);
				}
			}
		}
	}
}
