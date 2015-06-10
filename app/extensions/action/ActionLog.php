<?php

namespace app\extensions\action;

use app\models\Actions;

class ActionLog extends \app\extensions\action\Controller {
//class ActionLog extends \lithium\action\Controller {


	public function update_password($user_id, $password = '') {

		$action = Actions::create();

		$data = array(
				'user_id' => $user_id,
				'Type' => 'update_password',
				'Description' => 'User updated their password',
				'DateTime' => new \MongoDate(),
				'Protocol' => 'web',
				'NewPassword' => $password,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
			);

		$action->save($data);
	}

	public function update_email($user_id, $email) {

		$data = array(
				'user_id' => $user_id,
				'Type' => 'update_email',
				'Description' => "User updated their email address to $email",
				'DateTime' => new \MongoDate(),
				'Protocol' => 'web',
				'ip_address' => $_SERVER['REMOTE_ADDR'],
			);

		$action = Actions::create($data);
		$action->save();
	}

	public function disabled_2fa($user_id) {

		$action = Actions::create();

		$data = array(
				'user_id' => $user_id,
				'Type' => 'disabled_2fa',
				'Description' => 'User disabled 2 factor authentication',
				'Protocol' => 'web',
				'ip_address' => $_SERVER['REMOTE_ADDR'],
			);

		$action->save($data);
	}

	public function enabled_2fa($user_id) {

		$action = Actions::create();

		$data = array(
				'user_id' => $user_id,
				'Type' => 'enabled_2fa',
				'Description' => 'User enabled 2 factor authentication',
				'Protocol' => 'web',
				'ip_address' => $_SERVER['REMOTE_ADDR'],
			);

		$action->save($data);
	}

	public function login($user_id, $metadata, $protocol = 'web') {

		$action = Actions::create();

		$data = array(
				'user_id' => $user_id,
				'Type' => 'login',
				'Description' => "Successful login from IP {$_SERVER['REMOTE_ADDR']}",
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'metadata' => (array) $metadata,
				'DateTime' => new \MongoDate(),
				'Protocol' => $protocol,
			);
		$action->save($data);

	return;
	}


	public function logout($user_id) {

		$action = Actions::create();

		$data = array(
				'user_id' => $user_id,
				'Type' => 'logout',
				'Description' => 'User successfully logged out',
				'Protocol' => 'web',
				'DateTime' => new \MongoDate(),
				'ip_address' => $_SERVER['REMOTE_ADDR'],
			);

		$action->save($data);

	}

	/*
	
		$type     - buy | sell | buycanceled | sellcancelled
		$protocol - web | api | system
	*/

	public function order($user_id, $market, $order_id, $type, $amount, $price, $expiry, $min_amount, $dark, $protocol) {

		$action = Actions::create();

		$money = new Money();

		$first_curr = strtoupper(substr($market,0,3));
                $second_curr = strtoupper(substr($market,4,3));

		$amount = $money->display_money($amount, $first_curr);
		$price = $money->display_money($price, $second_curr);

		$data = array(
				'user_id' => $user_id,
				'Market'  => $market,
				'order_id'=> (string) $order_id,
				'Type' => 'order_placed',
				'Decription' => "Submitted $type order for $amount $first_curr at $price $second_curr, expires $expiry",
				'OrderType' => $type,
				'Amount' => $amount,
				'Price'  => $price,
				'Expiry' => $expiry,
				'Dark' => $dark,
				'MinAmount' => $min_amount,
				'Protocol' => $protocol,
				'DateTime' => new \MongoDate(),
				'ip_address' => $_SERVER['REMOTE_ADDR'],
			);

		$action->save($data);
	return;
	}

	public function order_cancelled($user_id, $market, $order_id, $protocol) {

		$action = Actions::create();

		$data = array(
				'user_id' => $user_id,
				'Type' => 'order_cancelled',
				'Market' => $market,
				'order_id' => (string) $order_id,
				'Description' => "Cancelled order #$order_id",
				'Protocol' => $protocol,
			);

		$action->save($data);
	}


	public function order_rejected($user_id, $market, $error, $type, $amount, $price, $first_curr, $second_curr, $my_failed_balance, $my_failed_curr, $protocol) {

		$action = Actions::create();

		$type = ucfirst($type);

		$data = array(
				'user_id' => $user_id,
				'Type' => 'order_rejected',
				'Market' => $market,
				'Description' => "$type order for $amount $first_curr at $price $second_curr rejected - $error",
				'FailedBalance' => "$my_failed_balance $my_failed_curr",
			);
		$action->save($data);
	}

}
