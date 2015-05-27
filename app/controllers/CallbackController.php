<?php
namespace app\controllers;

use app\models\Transactions;
use lithium\data\Connections;
use app\extensions\action\Coinprism;
use app\extensions\action\Money;

class CallbackController extends \lithium\action\Controller {


	public function index($tx_hash = false) {

	if( ($data['payload']['transaction_hash'] = $tx_hash) OR ($data = $this->request->data) ) {	

	/*
		We only care about the tx_hash, we get all the real info from coinprism
	*/	
	
		$coinprism = new Coinprism(COINPRISM_USERNAME, COINPRISM_PASSWORD);
		$transactions = $coinprism->get_transaction($data['payload']['transaction_hash']);
		

		$tx_hash = $transactions['hash'];
		$confirmations = $transactions['confirmations'];
		$status = $this->convert_status($confirmations);
	/*
		Would normally only expect one deposit per transaction, but just in case there is more than one due to a sendtomany transaction, we'll loop through each possibility

		$coinprism->get_transaction has done most of our work already, only returning outputs to addresses which belong to our users and seperating the transactions by currency
	*/

		foreach($transactions['deposits'] as $deposit) {

			//does the transaction already exist?
			$tx = Transactions::find('first', array(
			     'conditions' => array('TransactionHash' => $tx_hash, 'Address' => $deposit['address'], 'Currency' => $deposit['currency'], 'Amount' => (int) $deposit['amount'])
			));

			//if not, create it
				if($tx['_id']==""){
				$tx = Transactions::create();

					$data = array(
                                               'DateTime' => new \MongoDate(),
                                                'TransactionHash' => $tx_hash,
                                      		'user_id' => $deposit['user_id'],
					        //'username' => $details['username'], //use the user_id not username
                                                'Address'=>$deposit['address'],                                                      
                                                'Amount'=> (int) $deposit['amount'], //switched to integers!
                                                'Currency'=> $deposit['currency'],
			  			'Status' =>$this->convert_status($confirmations),                                             
						'TransactionType' => 'Deposit',
                                                'Added'=>false, //means the deposit has not been added to balance? That's how I'm using it here!
                                               	);

				} else { //already exists, so just update the status
					$data['Status'] = $status;
	
					//and the balance if relevant	
					if( ('completed' == $status) && ($tx['Added'] == false) ) {

						$money = new Money($deposit['user_id']);
						if($money->update_balance($deposit['amount'], $deposit['currency'])) $data['Added'] = true;
					}
				}


                         //save the transaction
                         $tx->save($data);
		}
			    
			return $this->render(array('layout' => false));
	
			} //end data posted

		return;
		}

	private function convert_status($confirmations) {

	if($confirmations == 0) return 'pending';
	elseif($confirmations >= 3) return 'completed';
	elseif($confirmations >= 1) return 'confirmed';
	}
}

?>
