<?php
namespace Raketa\Plastfoil\Loaders;

use Raketa\Plastfoil\Helpers\Main;
use Raketa\Plastfoil\Table\RaketaServiceRegistryTable;
use Bitrix\Main\Type;
class RegisterAuthorizedContractors
{
	public static function loader()
	{
		RaketaServiceRegistryTable::dropTable();
		RaketaServiceRegistryTable::createTable([
			'admin' => [
				'name' => 'RaketaServiceRegistry'
			],
			'lang' => [
				'ru' => 'Таблица реестров регистрации'
			]
		]);
		$pathToFile = realpath(__DIR__ . '/../../../../../upload/loaders/register_authorized_contractors.json');
		$dataJson = file_get_contents($pathToFile);
		$data = json_decode($dataJson, true);
		$loadData = [];
		foreach ($data as $item) {

			if(!empty($item['date_registration_legal_entity'])) {
				$format = 'm/d/Y';
				if(str_contains($item['date_registration_legal_entity'], '.')) {
					$format = 'd.m.Y';
				}
				$item['date_registration_legal_entity'] = $item['date_registration_legal_entity'] ? new Type\Date($item['date_registration_legal_entity'], $format) : '';
			}
			if(!empty($item['date_issue'])) {
				$format = 'm/d/Y';
				if(str_contains($item['date_issue'], '.')) {
					$format = 'd.m.Y';
				}
				$item['date_issue'] = $item['date_issue'] ? new Type\Date($item['date_issue'], $format) : '';
			}
			if(!empty($item['renewal_date'])) {
				$format = 'm/d/Y';
				if(str_contains($item['renewal_date'], '.')) {
					$format = 'd.m.Y';
				}
				$item['renewal_date'] = $item['renewal_date'] ? new Type\Date($item['renewal_date'], $format) : '';
			}
			if(!empty($item['constructive'])) {
				$item['constructive'] = mb_ucfirst($item['constructive']);
			}
			$arParams = [
				"replace_space"=> "-",
				"replace_other"=> "-"
			];
			$item['ogrn'] = preg_replace('/[^0-9.]+/', '', $item['ogrn']);
			$loadData[] = [
				'UF_INDEX' => $item['index'] ?? '',
				'UF_NAME' => $item['name'] ?? '',
				'UF_OGRN' => $item['ogrn'] ?? '',
				'UF_DATE_REGISTRATION' => $item['date_registration_legal_entity'] ?? null,
				'UF_EXTERNAL_ID' => $item['id'] ?? '',
				'UF_DATE_ISSUE' => $item['date_issue'],
				'UF_DATE_RENEWAL' => $item['renewal_date'],
				'UF_CITY' => $item['region_activity'] ?? '',
				'UF_RESPONSIBLE_EMPLOYEE' => $item['responsible_employee'] ?? '',
				'UF_AUTHORIZATION_TYPE' => $item['authorization_type'] ?? '',
				'UF_CONSTRUCTIVE' => $item['constructive'] ?? '',
				'UF_INFO' => $item['info'] ?? '',
				'UF_COUNTRY' => $item['country'] ?? '',
				'UF_FZ' => $item['fz'] ?? '',
				'UF_CODE' => \Cutil::translit($item['id'], "ru", $arParams)
			];
		}
		$dd = RaketaServiceRegistryTable::addMulti($loadData);
		dd($dd->getErrorMessages());
	}
}
