<?php
namespace app\controllers;

use app\extensions\action\Money;
use app\extensions\action\Coinprism;
use app\extensions\action\CallQueue;

class AvailabilityController extends \app\extensions\action\Controller {

        public function index() {

	new CallQueue();
	
	return 'Hello World';
        }

	public function live() {



	
		if ($this->request->data) {

		$watch_address = $this->request->data['Address'];
		$forward_amount = $this->request->data['Amount'];
		$min_amount = $this->request->data['MinAmount'];


		}

	return $this->render(['layout' => 'mobile']);

	}

}

?>
