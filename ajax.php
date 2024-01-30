<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule('wilpkron.bitrix_helper')) {
	return;
}

parse_str($_SERVER['REQUEST_URI'], $aUrlParams);

$sMethodName = '';
$sControllerName = '';

foreach ($aUrlParams as $sKey => $sValue) {
	if (strpos($sKey, 'act') !== false && $sValue) {
		$aValue = explode('.', $sValue);
		$aValue = array_map('ucfirst', $aValue);
		list($sControllerName, $sMethodName) = $aValue;
		break;
	}
}


try {
	if (!$sControllerName || !$sMethodName) {
		throw new \Exception('Отстутсвуют параметры контролера.');
	}

	$sControllerName = "Ajax$sControllerName";

	$requestMethodLower = strtolower($_SERVER['REQUEST_METHOD']);
	$requestMethodUcFirst = ucfirst($requestMethodLower);
	$sClassPath = RT_MODULE_DIR . "/lib/ajax/$requestMethodLower/$sControllerName";
	$sFileName = "$sClassPath.php";

	if (!file_exists($sFileName)) {
		throw new \Exception('Указано неверное наименование контроллера.');
	}

	$sClassName = "\\Wilp\\Ajax\\$requestMethodUcFirst\\$sControllerName";
	$sMethodName = ucfirst(strtolower($sMethodName));
	$sMethodName = "action$sMethodName";
	if (!class_exists($sClassName)) {
		throw new \Exception("Класса $sClassName не существует.");
	} elseif (!method_exists($sClassName, $sMethodName)) {
		throw new \Exception("Метода $sMethodName в классе $sClassName не существует.");
	}

	$mResponse = $sClassName::$sMethodName();

	$sHeader = 'Content-Type: text/html; charset=utf-8';
	if (!empty($mResponse) && $mResponse['type'] === 'html') {
		$mResponse = $mResponse['result'];
	} else {
		$sHeader = 'Content-Type: application/json; charset=utf-8';
		$mResponse = json_encode($mResponse, JSON_UNESCAPED_UNICODE);
	}

	header($sHeader);
	echo $mResponse;

} catch (\Exception $e) {
	http_response_code(500);
	echo $e->getMessage();
}
