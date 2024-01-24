<?php

namespace Wilp\Helpers;

use Bitrix\Main\Mail\Event;

class Sender
{
	public static function email($options = [])
	{
		$data = [
			"EVENT_NAME" => $options['EVENT_NAME'],
			"LID" => $options['SITE_ID'] ?? "s1",
			"C_FIELDS" => $options['C_FIELDS'],
		];

		if (empty($options['FILES'])) {
			return Event::send($data);
		} else {
			$data['FILE'] = $options['FILES'];
			if (!is_array($data['FILE'])) {
				$data['FILE'] = [$data['FILE']];
			}
			return Event::sendImmediate($data);
		}
	}
}
