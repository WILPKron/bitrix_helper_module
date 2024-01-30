<div class="wilp-editor-table-wrapper js-wilp-editor-table-wrapper" id="<?= $strHTMLControlName['VALUE'] ?>">
	<div class="table-container">
		<h4>Таблица для заполнения</h4>
		<table class="table">
			<thead class="js-wilp-editor-table-head">
				<tr></tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<div class="table-popup js-table-popup">
		<h5>Удалить:</h5>
		<div class="container">
			<span class="js-table-delete-row">Строку</span>
			<span class="js-table-delete-column">Столбец</span>
		</div>
		<h5>Добавить:</h5>
		<div class="container">
			<span class="js-table-add-column-left">Столбец слева</span>
			<span class="js-table-add-column-right">Столбец справа</span>
			<span class="js-table-add-row-top">Строку сверху</span>
			<span class="js-table-add-row-bottom">Строку снизу</span>
		</div>
		<span class="js-table-popup-close">Закрыть</span>
	</div>
	<div class="table-btn-wrapper">
		<span title="Скопировать таблицу" class="js-table-copy">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
				<path d="M16 12.9V17.1C16 20.6 14.6 22 11.1 22H6.9C3.4 22 2 20.6 2 17.1V12.9C2 9.4 3.4 8 6.9 8H11.1C14.6 8 16 9.4 16 12.9Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M22 6.9V11.1C22 14.6 20.6 16 17.1 16H16V12.9C16 9.4 14.6 8 11.1 8H8V6.9C8 3.4 9.4 2 12.9 2H17.1C20.6 2 22 3.4 22 6.9Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</span>
		<span title="Вставить данные" class="js-table-paste">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 24 24" version="1.1">
				<path d="M12.7533481,2 C13.9109409,2 14.8640519,2.87549091 14.9866651,4.00045683 L16.75,4 C17.940864,4 18.9156449,4.92516159 18.9948092,6.09595119 L19,6.25 C19,6.6291895 18.7182223,6.94256631 18.3526349,6.99216251 L18.249,6.999 C17.8698105,6.999 17.5564337,6.71722232 17.5068375,6.35163486 L17.5,6.25 C17.5,5.87030423 17.2178461,5.55650904 16.8517706,5.50684662 L16.75,5.5 L14.6176299,5.50081624 C14.2140619,6.09953034 13.5296904,6.49330383 12.7533481,6.49330383 L9.24665191,6.49330383 C8.47030963,6.49330383 7.78593808,6.09953034 7.38237013,5.50081624 L5.25,5.5 C4.87030423,5.5 4.55650904,5.78215388 4.50684662,6.14822944 L4.5,6.25 L4.5,19.754591 C4.5,20.1342868 4.78215388,20.448082 5.14822944,20.4977444 L5.25,20.504591 L8.25000001,20.5041182 C8.62963593,20.5040584 8.94342614,20.7861183 8.99313842,21.1521284 L9,21.254 C9,21.6682327 8.66423269,22.0040529 8.25000001,22.0041182 L5.25,22.004591 C4.05913601,22.004591 3.08435508,21.0794294 3.00519081,19.9086398 L3,19.754591 L3,6.25 C3,5.05913601 3.92516159,4.08435508 5.09595119,4.00519081 L5.25,4 L7.01333493,4.00045683 C7.13594814,2.87549091 8.0890591,2 9.24665191,2 L12.7533481,2 Z M18.75,8 C19.940864,8 20.9156449,8.92516159 20.9948092,10.0959512 L21,10.25 L21,19.75 C21,20.940864 20.0748384,21.9156449 18.9040488,21.9948092 L18.75,22 L12.25,22 C11.059136,22 10.0843551,21.0748384 10.0051908,19.9040488 L10,19.75 L10,10.25 C10,9.05913601 10.9251616,8.08435508 12.0959512,8.00519081 L12.25,8 L18.75,8 Z M12.7533481,3.5 L9.24665191,3.5 C8.83428745,3.5 8.5,3.83428745 8.5,4.24665191 C8.5,4.65901638 8.83428745,4.99330383 9.24665191,4.99330383 L12.7533481,4.99330383 C13.1657126,4.99330383 13.5,4.65901638 13.5,4.24665191 C13.5,3.83428745 13.1657126,3.5 12.7533481,3.5 Z" fill="white"></path>
			</svg>
		</span>
		<span title="Очистить таблицу" class="js-table-clear">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="white" height="20px" version="1.1" id="Layer_1" viewBox="0 0 512 512">
				<rect x="170.667" y="178.424" width="46.545" height="232.727"/>
				<rect x="294.788" y="178.424" width="46.545" height="232.727"/>
				<path d="M341.333,77.576V0H170.667v77.576H23.273v46.545h38.788V512h387.879V124.121h38.788V77.576H341.333z M217.212,46.545 h77.576v31.03h-77.576V46.545z M403.394,465.455H108.606V124.121h294.788V465.455z"/>
			</svg>
		</span>
	</div>
	<input class="js-table-input" type="hidden" name="<?= $strHTMLControlName["VALUE"] ?>" value="">
	<?php if (($arProperty["WITH_DESCRIPTION"] == "Y") && ('' != trim($strHTMLControlName["DESCRIPTION"]))): ?>
		<p>
			Описание: <input class="js-table-input" type="text" name="<?= $strHTMLControlName["DESCRIPTION"] ?>" value="<?=$value['DESCRIPTION']?>">
		</p>
	<? endif ?>
	<span class="table-description">Элементы управления таблицы вызываются нажатием правой кнопкой мыши</span>
	<?php if(!empty($additionalOptions)):?>
		<h4>Дополнительные поля:</h4>
		<div class="additional-fields">
			<?php foreach ($additionalOptions as $field):?>
				<?php
					$fieldSave = $value['VALUE']['ARRAY_HTML']['OPTIONS'][$field['name']] ?? [];
				?>
				<div class="additional-field">
					<?php if($field['type'] === 'text'):?>
						<?=$field['title']?>: <input
							name="<?=$field['name']?>"
							type="<?=$field['type']?>"
							value="<?=$fieldSave['value'] ?? ''?>"
							class="js-additional-field-value"
						>
					<?php elseif($field['type'] === 'select'):?>
						<?=$field['title']?>: <select name="<?=$field['name']?>" value="<?=$fieldSave['value'] ?? ''?>" class="js-additional-field-value">
							<?php foreach ($field['list'] as $item):?>
								<option value="<?=$item['value']?>"><?=$item['name'] ?? $item['value']?></option>
							<?php endforeach?>
						</select>
					<?php elseif($field['type'] === 'textarea'):?>
						<br><?=$field['title']?>: <br><br><textarea  class="js-additional-field-value" name="<?=$field['name']?>"><?=$fieldSave['value'] ?? ''?></textarea><br>
					<?php elseif($field['type'] === 'checkbox'):?>
						<?=$field['title']?>: <input
							type="checkbox"
							name="<?=$field['name']?>"
							class="js-additional-field-value"
							<?php if($item['value'] === true):?>
								checked
							<?php endif?>
						>
					<?else:?>
						Поле типа (<?=$field['type']?>) не поддерживается
					<?php endif?>
				</div>
			<?php endforeach?>
		</div>
	<?php endif?>
</div>
