<?php
/*
 * Файл local/modules/scrollup/options.php
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);

Loader::includeModule($module_id);

$aTabs = array(
	array(
		'DIV' => 'google',
		'TAB' => Loc::getMessage('RAKETA_TAB_GOOGLE'),
		'TITLE' => Loc::getMessage('RAKETA_TAB_GOOGLE'),
		'OPTIONS' => array(
			array('google_reCAPTCHA_site_key', Loc::getMessage('RAKETA_GOOGLE_SITE_KEY'), '', array('text', 30)),
			array('google_reCAPTCHA_privet_key', Loc::getMessage('RAKETA_GOOGLE_PRIVET_KEY'), '', array('text', 30)),
		)
	)
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);

$tabControl->begin();
?>
<form action="<?=$APPLICATION->getCurPage()?>?mid=<?=$module_id?>&lang=<?=LANGUAGE_ID?>" method="post">
	<?=bitrix_sessid_post(); ?>
	<?php
	foreach ($aTabs as $aTab) {
		if ($aTab['OPTIONS']) {
			$tabControl->beginNextTab();
			__AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
		}
	}
	$tabControl->buttons();
	?>
	<input type="submit" name="apply" value="<?= Loc::GetMessage('RAKETA_BTN_SAVE'); ?>" class="adm-btn-save"/>
	<input type="submit" name="default" value="<?= Loc::GetMessage('RAKETA_BTN_DEFAULT'); ?>"/>
</form>

<?php
$tabControl->end();

if ($request->isPost() && check_bitrix_sessid()) {
	foreach ($aTabs as $aTab) {
		foreach ($aTab['OPTIONS'] as $arOption) {
			if (!is_array($arOption)) { // если это название секции
				continue;
			}
			if ($arOption['note']) { // если это примечание
				continue;
			}
			if ($request['apply']) { // сохраняем введенные настройки
				$optionValue = $request->getPost($arOption[0]);
				Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
			} elseif ($request['default']) { // устанавливаем по умолчанию
				Option::set($module_id, $arOption[0], $arOption[2]);
			}
		}
	}
	LocalRedirect($APPLICATION->getCurPage() . '?mid=' . $module_id . '&lang=' . LANGUAGE_ID);
}
?>
