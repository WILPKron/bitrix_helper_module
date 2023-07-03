<?php

namespace Wilp\Ajax\Post;

use Wilp\Helpers\Response,
	Wilp\Helpers\Form,
	Wilp\Helpers\Sender;

class AjaxForm
{
	public static function actionCallback()
	{
		$request = Response::getRequestObj('AjaxForm/actionCallback');
		$errorField = Form::requiredField(
			['Name', 'Phone', 'Email', 'Textarea'],
			$request['postList']
		);
		if (!empty($errorField)) {
			return $request['response']->shapeError($errorField, "Обязательные поля пустые");
		}

		$sendOk = Sender::email([
			'EVENT_NAME' => 'FEEDBACK_FORM',
			'SITE_ID' => 's1',
			'C_FIELDS' => [
				"NAME" => $request['postList']['Name'],
				"TELEPHONE" => $request['postList']['Phone'],
				"EMAIL" => $request['postList']['Email'],
				"MESSAGE" => $request['postList']['Textarea'],
			]
		]);

		if ($sendOk) {
			return $request['response']->shapeOk([], 'Форма успешно отправлена');
		} else {
			return $request['response']->shapeError([], 'Возникла проблема');
		}
	}
}
