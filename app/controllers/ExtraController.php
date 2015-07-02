<?php
namespace app\controllers;

use app\extensions\action\Money;
use app\extensions\action\Coinprism;

class ExtraController extends \app\extensions\action\Controller {

        public function index() {

        return '';
        //      return;
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
