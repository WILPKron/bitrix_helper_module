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
				/*white-space: pre-line;*/
				background-color: black;
				color: white;
				display: block;
				padding: 15px;
			}
		</style>
		Примеры заполнения:
	</td>
	<td class="raketa-additional-options-setting-example">
		<h3>Структура данных</h3>
		<p>
			<b>css</b> - объект содерижащий в себе настройки отображения<br>
		<h4>Свойства:</h4>
		<ul>
			<li><b>tableColumn</b> - кол-во сталбцов в области отображения</li>
		</ul>
		<b>optionsList</b> - массив описания полей
		</p>
		<code>
			{<br />
			&nbsp; &quot;css&quot;: { ... },<br />
			&nbsp; &quot;optionsList&quot;: [ ... ]<br />
			}</code>

		<h3>Поле типа text:</h3>
		<code>
			{<br />
			&nbsp; &quot;type&quot;: &quot;text&quot;,<br />
			&nbsp; &quot;name&quot;: &quot;test_text&quot;,<br />
			&nbsp; &quot;title&quot;: &quot;Тестовое поле текст&quot;,<br />
			&nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; }<br />
			}
		</code>

		<h3>Поле типа textarea</h3>
		<code>
			{<br />
			&nbsp; &quot;type&quot;: &quot;textarea&quot;,<br />
			&nbsp; &quot;name&quot;: &quot;test_textarea&quot;,<br />
			&nbsp; &quot;title&quot;: &quot;Тестовое поле textarea&quot;,<br />
			&nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; }<br />
			}
		</code>
		<h3>Поле типа checkbox</h3>
		<code>
			{<br />
			&nbsp; &quot;type&quot;: &quot;checkbox&quot;,<br />
			&nbsp; &quot;name&quot;: &quot;test_checkbox&quot;,<br />
			&nbsp; &quot;title&quot;: &quot;Тестовое поле checkbox&quot;,<br />
			&nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; }<br />
			}
		</code>
		<h3>Поле типа select</h3>
		<code>
			{<br />
			&nbsp; &quot;type&quot;: &quot;select&quot;,<br />
			&nbsp; &quot;name&quot;: &quot;test_select&quot;,<br />
			&nbsp; &quot;title&quot;: &quot;Тестовое поле select&quot;,<br />
			&nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &quot;list&quot;: [<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Первое значение&quot;<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Второе значение&quot;<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Третье значение&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;name&quot;: &quot;Третье значение но название другое&quot;<br />
			&nbsp; &nbsp; }<br />
			&nbsp; ]<br />
			}
		</code>
		<h3>Поле типа radiolist</h3>
		<p>Поле представляет из себя список чекбоксов</p>
		<code>
			{<br />
			&nbsp; &quot;type&quot;: &quot;radiolist&quot;,<br />
			&nbsp; &quot;name&quot;: &quot;test_radiolist&quot;,<br />
			&nbsp; &quot;title&quot;: &quot;Тестовое поле radiolist&quot;,<br />
			&nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &quot;list&quot;: [<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Первое значение&quot;<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Второе значение&quot;<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Третье значение&quot;<br />
			&nbsp; &nbsp; }<br />
			&nbsp; ],<br />
			&nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; }<br />
			}
		</code>
		<h3>Поле типа bitrixfile</h3>
		<p>Поле представляет из себя стандартное поле для загрузки файлов в элементе инфоблока</p>
		<code>{<br />
			&nbsp; &quot;type&quot;: &quot;bitrixfile&quot;,<br />
			&nbsp; &quot;name&quot;: &quot;test_bitrixfile&quot;,<br />
			&nbsp; &quot;title&quot;: &quot;Тестовое поле bitrixfile&quot;,<br />
			&nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; }<br />
			}
		</code>
		<h3>Поле типа bitrixhtml</h3>
		<p>Поле представляет из себя стандартный редактор HTML</p>
		<code>{<br />
			&nbsp; &quot;type&quot;: &quot;bitrixhtml&quot;,<br />
			&nbsp; &quot;name&quot;: &quot;test_bitrixhtml&quot;,<br />
			&nbsp; &quot;title&quot;: &quot;Тестовое поле bitrixhtml&quot;,<br />
			&nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; }<br />
			}
		</code>
		<h3>Пример заполнения:</h3>
		<code>{<br />
			&nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &quot;tableColumn&quot;: 4<br />
			&nbsp; },<br />
			&nbsp; &quot;optionsList&quot;: [<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;type&quot;: &quot;text&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;name&quot;: &quot;test_text&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;title&quot;: &quot;Тестовое поле текст&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &nbsp; &nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; &nbsp; &nbsp; }<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;type&quot;: &quot;textarea&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;name&quot;: &quot;test_textarea&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;title&quot;: &quot;Тестовое поле textarea&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &nbsp; &nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; &nbsp; &nbsp; }<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;type&quot;: &quot;checkbox&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;name&quot;: &quot;test_checkbox&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;title&quot;: &quot;Тестовое поле checkbox&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &nbsp; &nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; &nbsp; &nbsp; }<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;type&quot;: &quot;select&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;name&quot;: &quot;test_select&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;title&quot;: &quot;Тестовое поле select&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &nbsp; &nbsp; &quot;list&quot;: [<br />
			&nbsp; &nbsp; &nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Первое значение&quot;<br />
			&nbsp; &nbsp; &nbsp; &nbsp; },<br />
			&nbsp; &nbsp; &nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Второе значение&quot;<br />
			&nbsp; &nbsp; &nbsp; &nbsp; },<br />
			&nbsp; &nbsp; &nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Третье значение&quot;,<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &quot;name&quot;: &quot;Третье значение но название другое&quot;<br />
			&nbsp; &nbsp; &nbsp; &nbsp; }<br />
			&nbsp; &nbsp; &nbsp; ]<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;type&quot;: &quot;radiolist&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;name&quot;: &quot;test_radiolist&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;title&quot;: &quot;Тестовое поле radiolist&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &nbsp; &nbsp; &quot;list&quot;: [<br />
			&nbsp; &nbsp; &nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Первое значение&quot;<br />
			&nbsp; &nbsp; &nbsp; &nbsp; },<br />
			&nbsp; &nbsp; &nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Второе значение&quot;<br />
			&nbsp; &nbsp; &nbsp; &nbsp; },<br />
			&nbsp; &nbsp; &nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &quot;value&quot;: &quot;Третье значение&quot;<br />
			&nbsp; &nbsp; &nbsp; &nbsp; }<br />
			&nbsp; &nbsp; &nbsp; ],<br />
			&nbsp; &nbsp; &nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; &nbsp; &nbsp; }<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;type&quot;: &quot;bitrixfile&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;name&quot;: &quot;test_bitrixfile&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;title&quot;: &quot;Тестовое поле bitrixfile&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &nbsp; &nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; &nbsp; &nbsp; }<br />
			&nbsp; &nbsp; },<br />
			&nbsp; &nbsp; {<br />
			&nbsp; &nbsp; &nbsp; &quot;type&quot;: &quot;bitrixhtml&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;name&quot;: &quot;test_bitrixhtml&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;title&quot;: &quot;Тестовое поле bitrixhtml&quot;,<br />
			&nbsp; &nbsp; &nbsp; &quot;sort&quot;: 100,<br />
			&nbsp; &nbsp; &nbsp; &quot;css&quot;: {<br />
			&nbsp; &nbsp; &nbsp; &nbsp; &quot;takesUpColumns&quot;: 1<br />
			&nbsp; &nbsp; &nbsp; }<br />
			&nbsp; &nbsp; }<br />
			&nbsp; ]<br />
			}
		</code>
	</td>
</tr>
