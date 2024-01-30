<?php

namespace Wilp\Base;

use Bitrix\Main\Loader;
class WilpCBitrixComponent extends \CBitrixComponent
{
	public function __construct($component = null)
	{

		foreach (['iblock', 'wilpkron.bitrix_helper'] as $module) {
			if(!Loader::includeModule($module)) {
				throw new \Exception('Модуль ' . $module . ' отсутсвует');
			}
		}

		parent::__construct($component);
	}
}
