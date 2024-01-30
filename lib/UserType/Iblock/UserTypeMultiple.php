<?php

namespace Wilp\UserType\Iblock;

use Bitrix\Main\DB\Exception,
	Bitrix\Main\Localization\Loc,
	Bitrix\Iblock,
	Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

class UserTypeMultiple
{
	const USER_TYPE = 'MULTIPLE_DOP_FIELDS';
	static public $CSS_INIT = false;
	public static function GetUserTypeDescription() //+ field_options
	{
		return array(
			"PROPERTY_TYPE" => Iblock\PropertyTable::TYPE_STRING,
			"USER_TYPE" => self::USER_TYPE,
			"DESCRIPTION" => "Составное поле",
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
	//Вызывается когда обработка идет через стандартные компоненты bitrix
	//для чего пока не понятно. Видимо для организации HTML вывода
	public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		return '';
	}

	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		echo 'GetAdminListViewHTML';
		return '';
	}

	public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
	{
		echo 'GetPublicEditHTML';
		return  [];
	}

	/**
	 * @return bool
	 */

	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) //+ redactor
	{
		if(!empty($value['VALUE']['OPTIONS'])) { //old field
			$value['VALUE'] = $value['VALUE']['OPTIONS'];
		}
		$additionalOptions = $arProperty['USER_TYPE_SETTINGS']['additional_options'] ?? [];
		$css = [];
		if(!empty($additionalOptions) && is_string($additionalOptions)) {
			try {
				$additionalOptions = json_decode($additionalOptions, JSON_UNESCAPED_UNICODE) ?? false;
				if($additionalOptions !== false) {
					if(!empty($additionalOptions['css'])) {
						$css = $additionalOptions['css'];
					}
					if(!empty($additionalOptions['optionsList'])) {
						$additionalOptions = $additionalOptions['optionsList'];
					}

					usort($additionalOptions, function ($a, $b) {
						return ($a['sort'] ?? 100) - ($b['sort'] ?? 100);
					});
				}
			} catch (Exception $exception) {
				$additionalOptions = false;
			}
		}
		ob_start();
		$bVarsFromForm = [];

		$s = preg_replace('#\[#', '|', $strHTMLControlName['VALUE']);
		$s = preg_replace('#\]#', '', $s);
		$s = explode('|', $s);
		$propertyValueID = $s[2];

		$sizeColumn = $css['tableColumn'] ?? 1;
		?>
		<style>
			#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields {
				padding: 20px;
				border: 2px dashed lightgray;
				margin-bottom: 10px;
				overflow: hidden;
			}
			#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields .additional-fields *:not(.adm-fileinput-item-saved) {
				box-sizing: border-box;
			}
			#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields.grid .additional-fields {
				display: grid;
				grid-gap: 10px;
				grid-template-columns: repeat(<?=$sizeColumn?>, 1fr);
			}
			#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields.grid .additional-field.additional-field__max {
				grid-column: 1 / -1;
			}

			<?php for($i = 1; $i <= $sizeColumn; $i++):?>
				#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields.grid .additional-field.additional-field__<?=$i?> {
					grid-column-end: span <?=$i?>;
				}
			<?php endfor?>
			#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields .additional-field h4 {
				margin-top: 0;
				margin-bottom: 10px;
			}
			#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields .error-message {
				color: red;
				font-size: 18px;
				margin-top: 20px;
				display: block;
			}
			#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields .additional-field-type__radiolist input {
				margin-left: 20px;
			}
			#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields .additional-field-type__text input {
				width: 100%;
			}
			#contaner_<?=$arProperty['ID']?>.wilp-field-files-additional-fields textarea {
				width: 100% !important;
				min-height: 100px;
			}
			#contaner_<?=$id?> .additional-field__description {
				  margin-top: 5px;
				  font-size: 10px;
				  color: hwb(0deg 0% 100%);
				  font-weight: 600;
			}
		</style>
		<?php


		echo '<div class="wilp-field-files-additional-fields grid" id="contaner_' . $arProperty['ID'] . '">';
		if(!empty($propertyValueID)) {
			echo '<input type="hidden" value="' . $propertyValueID . '" name="' . $strHTMLControlName['VALUE'] . '[PROPERTY_VALUE_ID]">';
		}
		?>
		<?php if(!empty($additionalOptions)):?>
			<div class="additional-fields">
				<?php foreach ($additionalOptions as $field):?>
					<?php
					$fieldSave = $value['VALUE'][$field['name']] ?? [];
					$name = $strHTMLControlName['VALUE'] . '[OPTIONS][' . $field['name'] . ']';
					$description = $strHTMLControlName['VALUE'] . '[OPTIONS][' . $field['name'] . '][DESCRIPTION]';
					$typeFieldName = $strHTMLControlName['VALUE'] . '[OPTIONS_TYPE][' . $field['name'] . ']';
					$cssIn = $field['css'];
					?>
					<div
						class="additional-field additional-field-type__<?=$field['type']?> <?=!empty($cssIn['takesUpColumns']) ? 'additional-field__' . $cssIn['takesUpColumns'] : ''?>"
					>
						<?php if($field['type'] === 'text'):?>
							<h4><?=$field['title']?>:</h4>
							<input
								name="<?=$name?>"
								type="<?=$field['type']?>"
								value="<?=$fieldSave['VALUE'] ?? ''?>"
							>
						<?php elseif($field['type'] === 'linetext'):?>
							<span><?=$field['value']?></span>
						<?php elseif($field['type'] === 'select'):?>
							<h4><?=$field['title']?>:</h4>
							<select name="<?=$name?>" value="<?=$fieldSave['VALUE'] ?? ''?>">
								<option value="">Нет</option>
								<?php foreach ($field['list'] as $item):?>
									<option
										value="<?=$item['value']?>"
										<?php if($fieldSave['VALUE'] === $item['value']):?>
											selected
										<?php endif?>
									><?=$item['name'] ?? $item['value']?></option>
								<?php endforeach?>
							</select>
						<?php elseif($field['type'] === 'textarea'):?>
							<h4><?=$field['title']?>:</h4>
							<textarea name="<?=$name?>"><?=$fieldSave['VALUE'] ?? ''?></textarea>
						<?php elseif($field['type'] === 'radiolist'):?>
							<h4><?=$field['title']?>:</h4>
							<table>
								<tbody>
									<tr>
										<td>Пустое</td>
										<td>
											<input
												type="radio"
												name="<?=$name?>"
												value=""
												<?=empty($fieldSave['VALUE']) ? 'checked' : ''?>
											>
										</td>
									</tr>
									<?php foreach ($field['list'] as $item):?>
										<tr>
											<td><?=$item['name'] ?? $item['value']?></td>
											<td>
												<input
													type="radio"
													name="<?=$name?>"
													value="<?=$item['value']?>"
													<?=$item['value'] === $fieldSave['VALUE'] ? 'checked' : ''?>
												>
											</td>
										</tr>
									<?php endforeach?>
								</tbody>
							</table>
						<?php elseif($field['type'] === 'checkbox'):?>
							<h4><?=$field['title']?>:</h4> <input
								type="checkbox"
								name="<?=$name?>"
								class="js-additional-field-value"
								<?php if(!empty($fieldSave)):?>
									checked
								<?php endif?>
							>
						<?php elseif($field['type'] === 'bitrixfile'):?>
							<?php
								$inputs = [];
								if(!empty($fieldSave['VALUE'])) {
									foreach ($fieldSave['VALUE'] as $key => $fileID) {
										$inputs[$name . "[" . $key . "]"] = $fileID;
									}
								}
								$options = array(
									"name" => $name . "[n#IND#]",
									"id" => $name . "[n#IND#]_".mt_rand(1, 1000000),
									"description" => true,
									"upload" => true,
									"allowUpload" => "F",
									"medialib" => true,
									"fileDialog" => true,
									"cloud" => true,
									"delete" => true,
									"maxCount" => 0
								);
								if(!empty($field['options'])) {
									$options = array_merge($options, $field['options']);
								}
							?>
							<h4><?=$field['title']?>:</h4>
							<?php
								echo \Bitrix\Main\UI\FileInput::createInstance($options)->show(
									$inputs ?? 0,
									$bVarsFromForm
								);
							?>
						<?php elseif($field['type'] === 'bitrixhtml'):?>
							<h4><?=$field['title']?>:</h4>
							<?php
								$LHE = new \CHTMLEditor;
								$LHE->Show(array(
									'name' => $name,
									'id' => $name . mt_rand(1, 1000000),
									'inputName' => $name,
									'content' => $fieldSave['VALUE'],
									'width' => '100%',
									'height' => '450',
									'bAllowPhp' => false,
									'limitPhpAccess' => false,
									'siteId' => SITE_ID,
									'relPath' => '/',
    								'templateId' => '.default',
									'setFocusAfterShow' => false
								));?>
						<?else:?>
							Поле типа (<?=$field['type']?>) не поддерживается
						<?php endif?>
						<?php if(!empty($field['description'])):?>
							<div class="additional-field__description">
								<?=$field['description']?>
							</div>
						<?php endif?>
						<input type="hidden" name="<?=$typeFieldName?>" value="<?=$field['type']?>">
					</div>
				<?php endforeach?>
			</div>
		<?php elseif ($additionalOptions === false):?>
			<span class="error-message">Поле дополнительных полей заполнено не верно</span>
		<?php endif?>
		</div>
		<?php

		$return = ob_get_contents();
		ob_end_clean();
		return  $return;
	}

	public static function fileWork($filesMass, $deleteFiles, $module = 'iblock')
	{
		$arResultFile = [];
		if(!empty($filesMass) && is_array($filesMass)) {
			$i = 0;
			if(!empty($deleteFiles)) {
				foreach ($deleteFiles as $key => $delStatus) {
					if(!empty($filesMass[$key]) && $delStatus === 'Y') {
						\CFile::Delete($filesMass[$key]);
						unset($filesMass[$key]);
					}
				}
			}
			foreach ($filesMass as $file) {
				if(is_array($file) && !empty($file['tmp_name'])) {
					$file = \CIBlock::makeFileArray($file);
					$file['MODULE_ID'] = $module;
					$id = \CFile::SaveFile($file, $module);
				} else {
					$id = $file;
				}
				if(!empty($id)) {
					$arResultFile[$i++] = $id;
				}
			}
		}

		return $arResultFile;
	}

	public static function ConvertToDB($arProperty, $value) //+ BD
	{
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		$postDataPropDelete = $request->getPost('PROP_del');

		$arResultOptions = [];

		if(!empty($value['VALUE']['OPTIONS'])) {
			foreach ($value['VALUE']['OPTIONS'] as $OPTION_KEY => $OPTION_VALUE) {

				$OPTION_TYPE = $value['VALUE']['OPTIONS_TYPE'][$OPTION_KEY];
				$VALUE = $OPTION_VALUE;

				if($OPTION_TYPE === 'bitrixfile') {
					$deleteFilesIn = [];
					$valueID = $value['VALUE']['PROPERTY_VALUE_ID'];
					$propertyID = $arProperty['ID'];

					if(!empty($postDataPropDelete[$propertyID][$valueID])) {
						$deleteFilesIn = $postDataPropDelete[$propertyID][$valueID]['VALUE']['OPTIONS'][$OPTION_KEY] ?? [];
					}
					$VALUE = self::fileWork($OPTION_VALUE, $deleteFilesIn);
				}

				if(!empty($VALUE) || $VALUE === 0 || $VALUE === '0') {
					$arResultOptions[$OPTION_KEY] = [
						'VALUE' => $VALUE,
						'TYPE' => $OPTION_TYPE,
						'KEY' => $OPTION_KEY
					];
				}
			}
		}

		$result = false;

		if(!empty($arResultOptions)) {
			$result = [];
			$result['VALUE'] = serialize($arResultOptions);
			$result['DESCRIPTION'] = '';
		}


		return $result;
	}

	public static function ConvertFromDB($arProperty, $value) //+ BD
	{
		$result = false;
		if(!empty($value['VALUE'])) {
			$result = [];
			$values = unserialize($value['VALUE']);
			if(!empty($values)) {
				$result['VALUE'] = $values;
				$result['DESCRIPTION'] = '';
			}
		}
		return $result;
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
		return true;
	}

	public static function GetLength($arProperty, $value)
	{
		if(is_array($value) && isset($value["VALUE"]))
			return mb_strlen(trim($value["VALUE"]));
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
			include __DIR__ . '/UserTypeMultiple/OptionsInfo.php';
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
		echo 'getValueFromString';
		return array(
			'VALUE' => []
		);
	}
}
