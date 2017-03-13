<?php

namespace app\extensions\action;

use app\models\Billing;

class Billing extends \lithium\action\Controller{


       public function __construct() {

        return;
        }

        public function new_charge($job_id, $visit_id, $contractor_id, $details, $currency, $amount, $invoice_id = false) {

		$charge = Billing::create();
		
		$data = array(
				'job_id' => $job_id,
				'visit_id' => $visit_id,
				'contractor_id' => $contractor_id,
				'details' => $details,
				'amount' => $amount,
			);
        }

}

