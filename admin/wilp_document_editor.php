<?php
/** @global CMain $APPLICATION */
/** @global CDatabase $DB */
/** @global CUser $USER */

use Bitrix\Main\Loader,
	Bitrix\Main,
	Bitrix\Iblock,
	Bitrix\Currency,
	Bitrix\Catalog,
	Wilp\Model\SectionDocumentsTable;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
Loader::includeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");
CJSCore::Init(["jquery"]);
$APPLICATION->SetTitle('Редактирование элемента');

$request = Main\Context::getCurrent()->getRequest();


if(!empty($request->get('action'))) {
	$sectionWork = new CIBlockSection();
	$sectionId = $request->get('SECTION_ID');
	$parameters = [
		'NAME' => $request->get('NAME') ?? '',
		'CODE' => $request->get('CODE') ?? '',
		'UF_TAGS' => $request->get('UF_TAGS') ?? '',
	];
	if(!empty($request->get('UF_FILE')) && is_array($request->get('UF_FILE'))) {
		$file = $request->get('UF_FILE');
		$file = \CIBlock::makeFileArray($file);
		$parameters['UF_FILE'] = $file;
	}

	$oldPS = $request->get('OLD_PARENT_SECTION') ?? [];
	$PS = $request->get('PARENT_SECTIONS') ?? [];

	if ($request->get('action') === 'save') {
		$sectionWork->Update($request->get('SECTION_ID'), $parameters);
	} else if ($request->get('action') === 'create') {
		$parameters['IBLOCK_ID'] = $_GET['IBLOCK_ID'];
		$sectionId = $sectionWork->Add($parameters);
		echo $sectionId;
	}

	if(!empty(array_diff($PS, $oldPS)) || !empty(array_diff($oldPS, $PS))) {
		$newParent = array_diff($PS, $oldPS);
		$deleteParent = array_diff($oldPS, $PS);

		foreach ($newParent as $parentId) {
			try {
				\Wilp\Table\WilpDocumentsSectionsLinkTable::add([
					'IBLOCK_SECTION_PARENT_ID' => $parentId,
					'IBLOCK_SECTION_CHILD_ID' => $sectionId
				]);
			} catch (Exception $exception) {}
		}
		foreach ($deleteParent as $parentId) {
			try {
				\Wilp\Table\WilpDocumentsSectionsLinkTable::delete([
					'parent' => $parentId, 'child' => $sectionId
				]);
			} catch (Exception $exception) {}
		}
	}

	exit();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$sectionData = SectionDocumentsTable::getList([
	'filter' => [
		'ID' => $_GET['SECTION_ID']
	],
	'select' => [
		'NAME', 'CODE', 'UF_FILE', 'ID', 'IBLOCK_ID', 'ACTIVE', 'SORT', 'UF_TAGS', 'PARENT' => 'PARENT_SECTIONS.ID'
	]
])->fetchAll();

$section = [];
$newElement = false;
if(!empty($sectionData)) {
	foreach ($sectionData as $item) {
		if(empty($section)) {
			$item['PARENT'] = [$item['PARENT']];
			$section = $item;
		} else {
			$section['PARENT'][] = $item['PARENT'];
		}
	}
} else {
	$newElement = true;
	if(!empty($_GET['PARENT_ID'])) {
		$section['PARENT'] = [$_GET['PARENT_ID']];
	}
}



$tagsData = SectionDocumentsTable::getList([
	'select' => ['UF_TAGS'],
	'filter' => ['!UF_FILE' => false]
])->fetchAll();
$tags = [];
foreach ($tagsData as $tagArray) {
	foreach ($tagArray['UF_TAGS'] as $tag) {
		$tags[] = $tag;
	}
}
$tags = array_unique($tags);

$tree = SectionDocumentsTable::getListTree(false, ['ID', 'NAME']);

if(!isset($tree[0])) {
	$tree = [ $tree ];
}
function treeBuilderSelect($tree, $i = 0, $section = [], $first = false) {
	$html = '';
	$temp = '';
	$i++;
	$colorList = [
		'0B64AD',
		'0C6EBE',
		'0D79D1',
		'0F85E6',
		'1092FD'
	];

	foreach ($tree as $item) {
		if($item['ID'] === $section['ID']) {
			continue;
		}
		if($item['NAME'] && $item['ID']) {
			if(!empty($section['PARENT'])) {
				$checked = in_array($item['ID'], $section['PARENT']) ? 'checked' : '';
			}

			$temp .= '<li value="'.$item['ID'].'"><input name="PARENT_SECTIONS[]" type="checkbox" '.$checked.' value="'.$item['ID'].'"/>'. $item['NAME'] .'</li>';
			if(!empty($item['CHILD'])) {
				$temp .= treeBuilderSelect($item['CHILD'], $i, $section);
			}
		}
	}

	if(!empty($temp)) {
		if($first) {
			$html .= '<ul class="container" style="background: #'.$colorList[0].'">';
		} else {
			$html .= '<ul style="background: #'.$colorList[$i].'">';
		}
			$html .= $temp;
		$html .= '</ul>';
	}

	return $html;
}

/****/
$inputs = [];
$name = 'UF_FILE';
if(!empty($section['UF_FILE'])) {
	$inputs[$name] = $section['UF_FILE'];
}
$options = array(
	"name" => 'UF_FILE', //[#IND#] - множественное поле  ! индекс важен, так как в php шаблоне эта метка будет заменяться
	"description" => false,
	"upload" => true,
	"allowUpload" => "I",
	"medialib" => false,
	"fileDialog" => true,
	"cloud" => false,
	"delete" => true,
	"edit" => true,
	//"allowSort" => "Y",
	"maxCount" => 1,
);

$bVarsFromForm = [];
/****/

?>
<style>
	.wilp-documents-form {
		background: white;
		padding: 40px;
	}
	.wilp-documents-form input[type="text"] {
		font-size: 20px;
		padding: 10px;
		width: 100%;
	}
	.wilp-documents-form select {
		font-size: 20px;
		padding: 10px;
		width: 100%;
		min-height: 1000px;
	}
	.wilp-documents-form ul {
		padding: 10px 5px 5px 10px;
		margin: 5px 5px 20px 10px;
		text-decoration: none;
		list-style: none;
	}
	.wilp-documents-form li {
		color: white;
		margin-bottom: 10px;
	}
	.wilp-documents-form .container {
		max-height: 700px;
		overflow-y: scroll;
		margin: 0 0 30px 0;
	}
	.wilp-documents-form .wilp-documents-content__toolBar button {
		background: white;
		color: #0B64AD;
		border-radius: 20px;
		padding: 10px 20px;
		border: 1px solid #0B64AD;
	}
	.wilp-documents-form .wilp-documents-content__toolBar button:hover {
		background: #0B64AD;
		color: white;
		cursor: pointer;
	}
	.wilp-documents-form .wilp-documents-content__toolBar {
		display: flex;
		justify-content: space-between;
	}
	.wilp-documents-form .wilp-documents-form__tags {
		background: #0A3A68;
		margin: 0;
	}

</style>
<div class="wilp-documents-content">
	<form class="wilp-documents-form" action="">
		<?php if(!empty($section['IBLOCK_ID'])):?>
			<input type="hidden" name="IBLOCK_ID" value="<?=$section['IBLOCK_ID']?>">
		<?php elseif (!empty($_GET['IBLOCK_ID'])):?>
			<input type="hidden" name="IBLOCK_ID" value="<?=$_GET['IBLOCK_ID']?>">
		<?php endif?>
		<?php if(!empty($section['ID'])):?>
			<input type="hidden" name="SECTION_ID" value="<?=$section['ID']?>">
		<?php endif?>
		<?php if ($newElement === false):?>
			<?php foreach ($section['PARENT'] as $parentId):?>
				<input type="hidden" name="OLD_PARENT_SECTION[]" value="<?=$parentId?>">
			<?php endforeach;?>
		<?php endif?>
		<lable>
			<h3>Название:</h3>
			<input type="text" name="NAME" value="<?=$section['NAME'] ?? ''?>">
		</lable>
		<lable>
			<h3>Символьный код:</h3>
			<input type="text" name="CODE" value="<?=$section['CODE'] ?? ''?>">
		</lable>
		<lable>
			<h3>Файл:</h3>
			<?=\Bitrix\Main\UI\FileInput::createInstance($options)->show(
				$inputs ?? 0,
				$bVarsFromForm
			);?>
		</lable>
		<lable>
			<h3>Теги файла:</h3>
			<ul class="wilp-documents-form__tags">
				<?php foreach ($tags as $tag):
					$checked = !empty($section['UF_TAGS']) && in_array($tag, $section['UF_TAGS'] ?? []) ? 'checked' : '';
					echo '<li><input name="UF_TAGS[]" type="checkbox" '.$checked.' value="'.$tag.'"/>'. $tag .'</li>';
					endforeach;
				?>
			</ul>
		</lable>
		<lable>
			<h3>Дерево:</h3>
			<?=treeBuilderSelect($tree, 0, $section, true)?>
		</lable>
		<div class="wilp-documents-content__toolBar">
			<?php if ($newElement === false):?>
				<button name="action" type="submit" value="save">Сохранить</button>
			<?php else:?>
				<button name="action" type="submit" value="create">Создать</button>
			<?php endif?>
			<button name="action" type="submit" value="cancel">Отмена</button>
		</div>
	</form>

</div>
<script>

	const saveAction = () => {
		const form = document.querySelector('form')
		const body = new FormData(form)
		body.append('action', 'save')
		fetch('', {
			method: 'POST',
			body
		}).then(res => res.text()).then(res => console.log(res))
	}

	const createAction = () => {
		const form = document.querySelector('form')
		const body = new FormData(form)
		body.append('action', 'create')
		fetch('', {
			method: 'POST',
			body
		}).then(res => res.text()).then(id => {
			if(id) {
				const urlParams = new URLSearchParams(window.location.search);
				urlParams.set('SECTION_ID', id);
				window.location.search = urlParams;
			}
		})
	}

	document.querySelector('button[name="action"]').addEventListener('click', event => {
		event.preventDefault();
		switch (event.currentTarget.value) {
			case "save":
				saveAction();
			break;
			case 'create':
				createAction();
				break;
			case 'cancel':
				location.reload()
			break;
		}
	})
</script>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>
