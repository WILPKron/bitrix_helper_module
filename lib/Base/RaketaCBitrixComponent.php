<?php

namespace Wilp\Base;

use Bitrix\Main\Loader;
class RaketaCBitrixComponent extends \CBitrixComponent
{
	public function __construct($component = null)
	{

		foreach (['iblock', 'raketa.plastfoil'] as $module) {
			if(!Loader::includeModule($module)) {
				throw new \Exception('Модуль ' . $module . ' отсутсвует');
			}
		}

		parent::__construct($component);
	}
}
