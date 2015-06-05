<?php

namespace app\extensions\action;

use app\models\ActionLog;

class ActionLogs extends \lithium\action\Controller{

	/*
	
		$type     - buy | sell | buycanceled | sellcancelled
		$protocol - web | api | system
	*/

	public function order($user_id, $market, $order_id, $type, $amount, $price, $expiry, $protocol) {

		$action = Actions::create();

		$data = array(
				'user_id' => $user_id,
				'market'  => $market,
				'order_id'=> $order_id,
				'Type' => $type,
				'Amount' => $amount,
				'Price'  => $price,
				'Expiry' => $expiry,
				'Protocol' => $protocol,
				'DateTime' => new \MongoDate(),
				'ip_address' => $_SERVER['REMOTE_ADDR'],
			);

		$action->save($data);
	}
}
