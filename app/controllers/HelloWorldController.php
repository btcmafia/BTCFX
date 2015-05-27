<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2013, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

use app\extensions\action\Coinprism;

class HelloWorldController extends \lithium\action\Controller {

	public function index($tx_hash) {

                $coinprism = new Coinprism(COINPRISM_USERNAME, COINPRISM_PASSWORD);
                $transactions = $coinprism->get_transaction($tx_hash);

		$print = print_r($transactions, true);
		
		return compact('transactions');
/*
if ($this->request->data) {

$id = $this->request->data['id'];
$address = $this->request->data['payload']['address'];
$sent = $this->request->data['payload']['sent'];
$received = $this->request->data['payload']['received'];
$txhash = $this->request->data['payload']['transaction_hash'];

$message = "Notification ID: $id\nAddress: $address\nSent: $sent\n Received: $received\n TX Hash: $txhash"; 
//	mail('stephen@joopla.co.uk', 'chain.com notification', $message);
}
*/

		return $this->render(array('layout' => false));
	}

	public function to_string() {
		return "Hello World";
	}

	public function to_json() {
		return $this->render(array('json' => 'Hello World'));
	}
}

?>
