<?php

namespace Raketa\Plastfoil\Ajax\Post;

use Raketa\Plastfoil\Helpers\Response;
use Raketa\Plastfoil\Services\WhereBuyService;

class AjaxWhereBuy
{
	public static function actionGet() {
		$request = Response::getRequestObj('AjaxWhereBuy/actionGet');

		$whereBuyService = new WhereBuyService();

		$data = $whereBuyService->getData();

		if (!empty($data)) {
			return $request['response']->shapeOk($data, 'Данные успешно получены');
		} else {
			return $request['response']->shapeError([], 'Возникла проблема');
		}
	}
}
