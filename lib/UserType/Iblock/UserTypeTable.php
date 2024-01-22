<?php

namespace Raketa\Plastfoil\UserType\Iblock;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class UserTypeTable
{
	const USER_TYPE = 'DYNAMIC_TABLE';

	public static function GetUserTypeDescription() //+ field_options
	{
		return array(
			"PROPERTY_TYPE" => Iblock\PropertyTable::TYPE_STRING,
			"USER_TYPE" => self::USER_TYPE,
			"DESCRIPTION" => "Динамические таблицы",
			"GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
			"GetPublicEditHTML" => array(__CLASS__, "GetPublicEditHTML"),
			"GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
			"GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
			"ConvertToDB" => array(__CLASS__, "ConvertToDB"),
			"ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
			"GetLength" =>array(__CLASS__, "GetLength"),
			"PrepareSettings" =>array(__CLASS__, "PrepareSettings"),
			"GetSettingsHTML" =>array(__CLASS__, "GetSettingsHTML"),
			"GetUIFilterProperty" => array(__CLASS__, "GetUIFilterProperty")
		);
	}

	public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		if (!is_array($value["VALUE"]))
			$value = static::ConvertFromDB($arProperty, $value);
		$ar = $value["VALUE"];
		if (!empty($ar) && is_array($ar))
		{
			if (isset($strHTMLControlName['MODE']) && $strHTMLControlName['MODE'] == 'CSV_EXPORT')
				return '['.$ar["TYPE"].']'.$ar["TEXT"];
			elseif (isset($strHTMLControlName['MODE']) && $strHTMLControlName['MODE'] == 'SIMPLE_TEXT')
				return ($ar["TYPE"] == 'HTML' ? strip_tags($ar["TEXT"]) : $ar["TEXT"]);
			else
				return FormatText($ar["TEXT"], $ar["TYPE"]);
		}

		return '';
	}

	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{

		if(!is_array($value["VALUE"]))
			$value = static::ConvertFromDB($arProperty, $value);
		$ar = $value["VALUE"];
		if($ar)
			return htmlspecialcharsEx($ar["TYPE"].":".$ar["TEXT"]);
		else
			return "&nbsp;";
	}

	public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
	{
		if (!Loader::includeModule("fileman"))
			return Loc::getMessage("IBLOCK_PROP_HTML_NOFILEMAN_ERROR");

		if (!is_array($value["VALUE"]))
			$value = static::ConvertFromDB($arProperty, $value);

		if (isset($strHTMLControlName["MODE"]) && $strHTMLControlName["MODE"]=="SIMPLE")
		{
			return '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[TYPE]" value="html">'
				.'<textarea cols="60" rows="10" name="'.$strHTMLControlName["VALUE"].'[TEXT]" style="width:100%">'.htmlspecialcharsEx($value["VALUE"]["TEXT"]).'</textarea>';
		}

		$id = preg_replace("/[^a-z0-9]/i", '', $strHTMLControlName['VALUE']);

		ob_start();
		echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[TYPE]" value="html">';
		$LHE = new CHTMLEditor;
		$LHE->Show(array(
			'name' => $strHTMLControlName["VALUE"].'[TEXT]',
			'id' => $id,
			'inputName' => $strHTMLControlName["VALUE"].'[TEXT]',
			'content' => $value["VALUE"]['TEXT'],
			'width' => '100%',
			'minBodyWidth' => 350,
			'normalBodyWidth' => 555,
			'height' => '200',
			'bAllowPhp' => false,
			'limitPhpAccess' => false,
			'autoResize' => true,
			'autoResizeOffset' => 40,
			'useFileDialogs' => false,
			'saveOnBlur' => true,
			'showTaskbars' => false,
			'showNodeNavi' => false,
			'askBeforeUnloadPage' => true,
			'bbCode' => false,
			'actionUrl' => '/bitrix/tools/html_editor_action.php',
			'siteId' => SITE_ID,
			'setFocusAfterShow' => false,
			'controlsMap' => array(
				array('id' => 'Bold', 'compact' => true, 'sort' => 80),
				array('id' => 'Italic', 'compact' => true, 'sort' => 90),
				array('id' => 'Underline', 'compact' => true, 'sort' => 100),
				array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
				array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
				array('id' => 'Color', 'compact' => true, 'sort' => 130),
				array('id' => 'FontSelector', 'compact' => false, 'sort' => 135),
				array('id' => 'FontSize', 'compact' => false, 'sort' => 140),
				array('separator' => true, 'compact' => false, 'sort' => 145),
				array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
				array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
				array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
				array('separator' => true, 'compact' => false, 'sort' => 200),
				array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
				array('id' => 'InsertImage', 'compact' => false, 'sort' => 220),
				array('id' => 'InsertVideo', 'compact' => true, 'sort' => 230),
				array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
				array('separator' => true, 'compact' => false, 'sort' => 290),
				array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310),
				array('id' => 'More', 'compact' => true, 'sort' => 400)
			),
		));
		$s = ob_get_contents();
		ob_end_clean();
		return  $s;
	}

	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) //+ redactor
	{
		global $raketaTableEditor;
		$additionalOptions = $arProperty['USER_TYPE_SETTINGS']['additional_options'] ?? false;
		if($additionalOptions && is_string($additionalOptions)) {
			try {
				$additionalOptions = json_decode($additionalOptions, JSON_UNESCAPED_UNICODE);
			} catch (Exception $exception) {
				$additionalOptions = [];
			}
		}
		ob_start();
		?>
			<?php if(empty($raketaTableEditor)):?>
				<style>
					<?=file_get_contents(__DIR__ . '/UserTypeTable/Editor/RaketaTableController.css')?>
				</style>
				<?php $raketaTableEditor = true?>
			<?php endif?>
			<?php include __DIR__ . '/UserTypeTable/Editor/index.php'?>
			<script>
				(() => {
					<?=file_get_contents(__DIR__ . '/UserTypeTable/Editor/RaketaTableController.js')?>
					let tableController = new raketaTableController('<?=$strHTMLControlName['VALUE']?>', '<?=$value['VALUE']['JSON'] ?? ''?>');
				})()
			</script>
		<?php
		$return = ob_get_contents();
		ob_end_clean();
		return  $return;
	}

	public static function ConvertToDB($arProperty, $value) //+ BD
	{
		$result = false;
		if(!empty($value['VALUE'])) {
			$result = [
				'VALUE' => $value['VALUE'],
				'DESCRIPTION' => $value['DESCRIPTION'],
			];
		}
		return $result;
	}

	public static function ConvertFromDB($arProperty, $value) //+ BD
	{
		$result = [];

		if (!is_array($value["VALUE"]) && !empty($value['VALUE'])) {
			$result = [
				'VALUE' => [
					'ARRAY_HTML' => json_decode(htmlspecialchars_decode($value['VALUE']), JSON_UNESCAPED_UNICODE),
					'JSON' => htmlspecialchars_decode($value['VALUE'])
				],
				'DESCRIPTION' => $value['DESCRIPTION']
			];
		}
		return count($result) > 0 ? $result : false;
	}

	/**
	 * Check value.
	 *
	 * @param bool|array $arFields			Current value.
	 * @param bool $defaultValue			Is default value.
	 * @return array|bool
	 */
	public static function CheckArray($arFields = false, $defaultValue = false)
	{
		$defaultValue = ($defaultValue === true);
		if (!is_array($arFields))
		{
			$return = false;
			if (CheckSerializedData($arFields))
				$return = unserialize($arFields);
		}
		else
		{
			$return = $arFields;
		}

		if ($return)
		{
			if (is_set($return, "TEXT") && ((trim($return["TEXT"]) <> '') || $defaultValue))
			{
				$return["TYPE"] = mb_strtoupper($return["TYPE"]);
				if (($return["TYPE"] != "TEXT") && ($return["TYPE"] != "HTML"))
					$return["TYPE"] = "HTML";
			}
			else
			{
				$return = false;
			}
		}
		return $return;
	}

	public static function GetLength($arProperty, $value)
	{
		if(is_array($value) && isset($value["VALUE"]["TEXT"]))
			return mb_strlen(trim($value["VALUE"]["TEXT"]));
		else
			return 0;
	}

	public static function PrepareSettings($arProperty) //+ options
	{
		return [
			"additional_options" =>  $arProperty["USER_TYPE_SETTINGS"]["additional_options"] ?? '',
		];
	}

	public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields) //+ options
	{
		$arPropertyFields = array(
			"HIDE" => array("ROW_COUNT", "COL_COUNT"),
		);
		ob_start();
		?>

			<tr valign="top">
				<td>Дополнительные поля:</td>
				<td>
					<style> .raketa-additional-options-setting { width: 100%; min-height: 200px; } </style>
					<textarea
						name="<?=$strHTMLControlName["NAME"]?>[additional_options]"
						class="raketa-additional-options-setting"
					><?=$arProperty["USER_TYPE_SETTINGS"]["additional_options"] ?? ''?></textarea>
				</td>
			</tr>
			<tr valign="top">
				<td>
					<style>
						.raketa-additional-options-setting-example code {
							white-space: pre-line;
							background-color: black;
							color: white;
							display: block;
							padding: 15px;
						}
					</style>
					Примеры заполнения:
				</td>
				<td class="raketa-additional-options-setting-example">
					<span>Все поля оборачиваются в [field, field, field] скобки все данные заполняется в стиле JSON</span><br><br>
					<div class="raketa-example-container">
						<div class="raketa-example-line">
							<h5>Текстовое поле</h5>
							<code>{
								&nbsp;&nbsp;"type":"text",
								&nbsp;&nbsp;"name":"test_input",
								&nbsp;&nbsp;"title":"Тестовый инпут"
							}</code>
						</div>
						<div class="raketa-example-line">
							<h5>Чекбокс</h5>
							<code>{
								&nbsp;&nbsp;"type": "checkbox",
								&nbsp;&nbsp;"name": "test_checkbox",
								&nbsp;&nbsp;"title": "Тестовый чекбокс"
							}</code>
						</div>
						<div class="raketa-example-line">
							<h5>Большое текстовое поле</h5>
							<code>{
								&nbsp;&nbsp;"type": "textarea",
								&nbsp;&nbsp;"name": "test_textarea",
								&nbsp;&nbsp;"title": "Тестовое большое текстовое поле"
							}</code>
						</div>
						<div class="raketa-example-line">
							<h5>Список</h5>
							<code>{
								&nbsp;&nbsp;"type": "select",
								&nbsp;&nbsp;"name": "test_field",
								&nbsp;&nbsp;"title": "тестовое поле",
								&nbsp;&nbsp;"list": [
								&nbsp;&nbsp;&nbsp;&nbsp;{
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"value": "Первое значение"
								&nbsp;&nbsp;&nbsp;&nbsp;},
								&nbsp;&nbsp;&nbsp;&nbsp;{
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"value": "Второе значение"
								&nbsp;&nbsp;&nbsp;&nbsp;},
								&nbsp;&nbsp;&nbsp;&nbsp;{
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"value": "Третье значение",
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "Третье значение но название другое"
								&nbsp;&nbsp;&nbsp;&nbsp;}
								&nbsp;&nbsp;]
							}</code>
						</div>
					</div>
				</td>
			</tr>
		<?php
		$return = ob_get_contents();
		ob_end_clean();
		return  $return;
	}

	/**
	 * @param array $property
	 * @param array $strHTMLControlName
	 * @param array &$fields
	 * @return void
	 */
	public static function GetUIFilterProperty($property, $strHTMLControlName, &$fields)
	{
		$fields["type"] = "string";
		$fields["operators"] = array(
			"default" => "%"
		);
		$fields["filterable"] = "?";
	}

	protected static function getValueFromString($value, $getFull = false)
	{
		$getFull = ($getFull === true);
		$valueType = 'HTML';
		$value = (string)$value;
		if ($value !== '')
		{
			$prefix = mb_strtoupper(mb_substr($value, 0, 6));
			$isText = $prefix == '[TEXT]';
			if ($prefix == '[HTML]' || $isText)
			{
				if ($isText)
					$valueType = 'TEXT';
				$value = mb_substr($value, 6);
			}
		}
		if ($getFull)
		{
			return array(
				'VALUE' => array(
					'TEXT' => $value,
					'TYPE' => $valueType
				)
			);
		}
		else
		{
			return array(
				'TEXT' => $value,
				'TYPE' => $valueType
			);
		}
	}
}
