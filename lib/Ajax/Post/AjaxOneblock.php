<?php

namespace Wilp\Ajax\Post;

use Wilp\Helpers\Response;
use Wilp\Table\OnePageIBlockTable;

class AjaxOneblock
{
	public static function actionCreated()
	{
		$request = Response::getRequestObj('AjaxOneblock/actionCreated');

		if(!\CModule::IncludeModule('iblock')) {
			return false;
		}

		$ib = new \CIBlock;

		$IBLOCK_TYPE = $request['postList']['iblock_type'];
		$SITE_ID = $request['postList']['site_id'];


		// Айдишники групп, которым будем давать доступ на инфоблок
		// $contentGroupId = $this->GetGroupIdByCode('CONTENT');
		// $editorGroupId = $this->GetGroupIdByCode('EDITOR');
		// $ownerGroupId = $this->GetGroupIdByCode('OWNER');

		//===================================//
		// Создаем инфоблок каталога товаров //
		//===================================//

		// Настройка доступа

		$arAccess = [ '2' => 'R' ];

		// if ($contentGroupId) $arAccess[$contentGroupId] = 'X'; // Полный доступ
		// if ($editorGroupId) $arAccess[$editorGroupId] = 'W'; // Запись
		// if ($ownerGroupId) $arAccess[$ownerGroupId] = 'X'; // Полный доступ

		$db = \CIBlock::GetList([],[
			'CODE' => $request['postList']['iblock_code'],
			'TYPE' => $request['postList']['iblock_type'],
		]);

		$iblock = $db->fetch();

		if(!empty($iblock)) {
			return $request['response']->shapeError([], 'Такой инфоблок уже есть');
		}

		$arFields = Array(
			'ACTIVE' => 'Y',
			'NAME' => $request['postList']['iblock_name'],
			'CODE' => $request['postList']['iblock_code'],
			'IBLOCK_TYPE_ID' => $IBLOCK_TYPE,
			'SITE_ID' => $SITE_ID,
			'SORT' => '5',
			'GROUP_ID' => $arAccess, // Права доступа
			'FIELDS' => array(
				'DETAIL_PICTURE' => array(
					'IS_REQUIRED' => 'N', // не обязательное
					'DEFAULT_VALUE' => array(
						'SCALE' => 'Y', // возможные значения: Y|N. Если равно 'Y', то изображение будет отмасштабировано.
						'WIDTH' => '600', // целое число. Размер картинки будет изменен таким образом, что ее ширина не будет превышать значения этого поля.
						'HEIGHT' => '600', // целое число. Размер картинки будет изменен таким образом, что ее высота не будет превышать значения этого поля.
						'IGNORE_ERRORS' => 'Y', // возможные значения: Y|N. Если во время изменения размера картинки были ошибки, то при значении 'N' будет сгенерирована ошибка.
						'METHOD' => 'resample', // возможные значения: resample или пусто. Значение поля равное 'resample' приведет к использованию функции масштабирования imagecopyresampled, а не imagecopyresized. Это более качественный метод, но требует больше серверных ресурсов.
						'COMPRESSION' => '95', // целое от 0 до 100. Если значение больше 0, то для изображений jpeg оно будет использовано как параметр компрессии. 100 соответствует наилучшему качеству при большем размере файла.
					),
				),
				'PREVIEW_PICTURE' => array(
					'IS_REQUIRED' => 'N', // не обязательное
					'DEFAULT_VALUE' => array(
						'SCALE' => 'Y', // возможные значения: Y|N. Если равно 'Y', то изображение будет отмасштабировано.
						'WIDTH' => '140', // целое число. Размер картинки будет изменен таким образом, что ее ширина не будет превышать значения этого поля.
						'HEIGHT' => '140', // целое число. Размер картинки будет изменен таким образом, что ее высота не будет превышать значения этого поля.
						'IGNORE_ERRORS' => 'Y', // возможные значения: Y|N. Если во время изменения размера картинки были ошибки, то при значении 'N' будет сгенерирована ошибка.
						'METHOD' => 'resample', // возможные значения: resample или пусто. Значение поля равное 'resample' приведет к использованию функции масштабирования imagecopyresampled, а не imagecopyresized. Это более качественный метод, но требует больше серверных ресурсов.
						'COMPRESSION' => '95', // целое от 0 до 100. Если значение больше 0, то для изображений jpeg оно будет использовано как параметр компрессии. 100 соответствует наилучшему качеству при большем размере файла.
						'FROM_DETAIL' => 'Y', // возможные значения: Y|N. Указывает на необходимость генерации картинки предварительного просмотра из детальной.
						'DELETE_WITH_DETAIL' => 'Y', // возможные значения: Y|N. Указывает на необходимость удаления картинки предварительного просмотра при удалении детальной.
						'UPDATE_WITH_DETAIL' => 'Y', // возможные значения: Y|N. Указывает на необходимость обновления картинки предварительного просмотра при изменении детальной.
					),
				),
				'SECTION_PICTURE' => array(
					'IS_REQUIRED' => 'N', // не обязательное
					'DEFAULT_VALUE' => array(
						'SCALE' => 'Y', // возможные значения: Y|N. Если равно 'Y', то изображение будет отмасштабировано.
						'WIDTH' => '235', // целое число. Размер картинки будет изменен таким образом, что ее ширина не будет превышать значения этого поля.
						'HEIGHT' => '235', // целое число. Размер картинки будет изменен таким образом, что ее высота не будет превышать значения этого поля.
						'IGNORE_ERRORS' => 'Y', // возможные значения: Y|N. Если во время изменения размера картинки были ошибки, то при значении 'N' будет сгенерирована ошибка.
						'METHOD' => 'resample', // возможные значения: resample или пусто. Значение поля равное 'resample' приведет к использованию функции масштабирования imagecopyresampled, а не imagecopyresized. Это более качественный метод, но требует больше серверных ресурсов.
						'COMPRESSION' => '95', // целое от 0 до 100. Если значение больше 0, то для изображений jpeg оно будет использовано как параметр компрессии. 100 соответствует наилучшему качеству при большем размере файла.
						'FROM_DETAIL' => 'Y', // возможные значения: Y|N. Указывает на необходимость генерации картинки предварительного просмотра из детальной.
						'DELETE_WITH_DETAIL' => 'Y', // возможные значения: Y|N. Указывает на необходимость удаления картинки предварительного просмотра при удалении детальной.
						'UPDATE_WITH_DETAIL' => 'Y', // возможные значения: Y|N. Указывает на необходимость обновления картинки предварительного просмотра при изменении детальной.
					),
				),
				// Символьный код элементов
				'CODE' => array(
					'IS_REQUIRED' => 'N', // Обязательное
					'DEFAULT_VALUE' => array(
						'UNIQUE' => 'Y', // Проверять на уникальность
						'TRANSLITERATION' => 'Y', // Транслитерировать
						'TRANS_LEN' => '30', // Максмальная длина транслитерации
						'TRANS_CASE' => 'L', // Приводить к нижнему регистру
						'TRANS_SPACE' => '-', // Символы для замены
						'TRANS_OTHER' => '-',
						'TRANS_EAT' => 'Y',
						'USE_GOOGLE' => 'N',
					),
				),
				// Символьный код разделов
				'SECTION_CODE' => array(
					'IS_REQUIRED' => 'N',
					'DEFAULT_VALUE' => array(
						'UNIQUE' => 'N',
						'TRANSLITERATION' => 'Y',
						'TRANS_LEN' => '30',
						'TRANS_CASE' => 'L',
						'TRANS_SPACE' => '-',
						'TRANS_OTHER' => '-',
						'TRANS_EAT' => 'Y',
						'USE_GOOGLE' => 'N',
					),
				),
				'DETAIL_TEXT_TYPE' => array(      // Тип детального описания
					'DEFAULT_VALUE' => 'html',
				),
				'SECTION_DESCRIPTION_TYPE' => array(
					'DEFAULT_VALUE' => 'html',
				),
				'IBLOCK_SECTION' => array(         // Привязка к разделам обязательноа
					'IS_REQUIRED' => 'N',
				),
				'LOG_SECTION_ADD' => array('IS_REQUIRED' => 'N'), // Журналирование
				'LOG_SECTION_EDIT' => array('IS_REQUIRED' => 'N'),
				'LOG_SECTION_DELETE' => array('IS_REQUIRED' => 'N'),
				'LOG_ELEMENT_ADD' => array('IS_REQUIRED' => 'N'),
				'LOG_ELEMENT_EDIT' => array('IS_REQUIRED' => 'N'),
				'LOG_ELEMENT_DELETE' => array('IS_REQUIRED' => 'N'),
			),

			// Шаблоны страниц
			'LIST_PAGE_URL' => '#SITE_DIR#/'. $request['postList']['iblock_code'] .'/',
			'SECTION_PAGE_URL' => '',
			'DETAIL_PAGE_URL' => '#SITE_DIR#/' . $request['postList']['iblock_code'] . '/#ELEMENT_CODE#/',

			'INDEX_SECTION' => 'N', // Индексировать разделы для модуля поиска
			'INDEX_ELEMENT' => 'N', // Индексировать элементы для модуля поиска

			'VERSION' => 1, // Хранение элементов в общей таблице

			'ELEMENT_NAME' => 'Товар',
			'ELEMENTS_NAME' => 'Товары',
			'ELEMENT_ADD' => 'Добавить товар',
			'ELEMENT_EDIT' => 'Изменить товар',
			'ELEMENT_DELETE' => 'Удалить товар',
			'SECTION_NAME' => 'Категории',
			'SECTIONS_NAME' => 'Категория',
			'SECTION_ADD' => 'Добавить категорию',
			'SECTION_EDIT' => 'Изменить категорию',
			'SECTION_DELETE' => 'Удалить категорию',

			'SECTION_PROPERTY' => 'N', // Разделы каталога имеют свои свойства (нужно для модуля интернет-магазина)
		);

		$ID = $ib->Add($arFields);

		if ($ID > 0) {
			global $USER;

			$el = new \CIBlockElement;

			$arLoadProductArray = Array(
				'MODIFIED_BY'    => $USER->GetID(),
				'CODE' => $request['postList']['iblock_code'],
				'IBLOCK_SECTION_ID' => false,
				'IBLOCK_ID'      => $ID,
				'NAME'           => $request['postList']['iblock_name'],
				'ACTIVE'         => 'Y',
			);

			if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
				OnePageIBlockTable::add([
					'UF_IBLOCK_CODE' => $request['postList']['iblock_code'],
					'UF_IBLOCK_TYPE' => $request['postList']['iblock_type']
				]);
				return $request['response']->shapeOk([], 'Инфоблок успешно создан');
			} else {
				\CIBlock::Delete($ID);
				return $request['response']->shapeError([], $el->LAST_ERROR);
			}
		} else {
			return $request['response']->shapeError([], $ib->LAST_ERROR);
		}
	}
}
