<?php
namespace app\controllers;

use app\extensions\action\Money;
use app\extensions\action\Coinprism;
use app\models\FeeForwarding;

class CustomController extends \app\extensions\action\Controller {

        public function index() {

	return 'CUSTOM API';
        }


	/*
	  Designed to ensure when we sell colored coins directly that the recipient has enough Bitcoin to use them.
	  Should hopefully shorten the learning curve for new users without technical knowledge.

	  It works by monitoring Steve's sending address. When he sends CC, it will check the recipient's corresponding BTC address, if it has no Bitcoins then we'll send some dust.
	*/
	public function senddust($tx_hash = false) {


		//We either received POST data (so use that), or tx_hash was passed in the url.	
	        if( ($data['payload']['transaction_hash'] = $tx_hash) OR ($data = $this->request->data) ) {

		$tx_hash = $data['payload']['transaction_hash'];

		//check if it's already been processed
		$check = FeeForwarding::find('first', array(
					'conditions' => array(
						'tx_hash' => $tx_hash,
						)
					));

		if(0 != count($check)) return;


        /*
                We only care about the tx_hash, we get all the real info from coinprism
        */


		$response = Coinprism::get_transaction($tx_hash, 'ALL');

        	if(!$response) {
                	        return array('error'=>'Unable to connect to Coinprism API.');
                }

        
       		$confirmations = $response->confirmations;

		//we are not likely to attempt a double spend ourselves!
		//if(0 == $confirmations) return; 


		if(0 == count($response->outputs)) return array('error'=>'No outputs, tx hash is probably invalid');

			foreach($response->outputs as $output) {

				//only interested in CC outputs that don't belong to the sending address, or any user's deposit addresses
				if( ( ($output->asset_id == DCT_ASSET_ID) OR ($output->asset_id == TCP_ASSET_ID) ) 
				&& ($output->addresses[0] != CUSTOM_DUST_ADDRESS) 
				&& ($output->addresses[0] != CUSTOM_WATCH_ADDRESS) 
				&& (! Coinprism::search_address($output->addresses[0])) ) {

					$addr = Coinprism::get_address($output->addresses[0]);

					if($addr['btc_address']['balance'] == 0) { $outputs[] = array('address' => $addr['btc_address']['address'], 'amount' => CUSTOM_DUST_AMOUNT); }
				}
			}

		if(0 == count($outputs)) return array('error'=>'No relevant outputs, perhaps watch address just receiving money.');

	$input = array('address' => CUSTOM_DUST_ADDRESS, 'key' => CUSTOM_DUST_KEY);

	$msg = "<p>About to send the following transactions:</p>";

	$msg .= "<p>Input: " . print_r($input, true) . "</p>";
	$msg .= "<p>Outputs: " . print_r($outputs, true) . "</p>";

	$sent_hash = Coinprism::send($input, $outputs);	

	$save = FeeForwarding::create();

	$data = array(
			'tx_hash' => $tx_hash,
			'outputs' => (array) $outputs,
			'total_sent' => count($outputs) * CUSTOM_DUST_AMOUNT,
			'sent_hash' => $sent_hash,
		    );

	$save->save($data);	

	//return compact('msg');
	return $this->render(array('layout' => false));

	} else {

	$msg = 'No data';
	return compact('msg');

	}
}

}

?>
