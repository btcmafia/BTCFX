<?php
namespace app\controllers;

use app\extensions\action\Money;
use app\extensions\action\Coinprism;
use app\extensions\action\CallQueue;

class ExtraController extends \app\extensions\action\Controller {

        public function index() {

	new CallQueue();
	
	return 'Hello World';
        }

	public function fee_forwarding() {

	
		if ($this->request->data) {

		$watch_address = $this->request->data['Address'];
		$forward_amount = $this->request->data['Amount'];
		$min_amount = $this->request->data['MinAmount'];


		}

	}

}

?>
