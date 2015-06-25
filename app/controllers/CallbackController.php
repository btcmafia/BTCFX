<?php
namespace app\controllers;

use app\models\Addresses;
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

		//check it isn't us sending the fee for forwarding
		//todo: think of a more robust way of checking this
		if( (DEFAULT_TRANSACTION_FEE == $transactions['deposits'][0]['amount']) && ('BTC' == $transactions['deposits'][0]['currency']) ) {

			 return $this->render(array('layout' => false));
		}


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



		/*
			if a btc deposit then we can forward it as soon as it has one confirmation

			if an asset deposit then we need to send enough btc to the address to pay the transaction fee when forwarding, then 2nd time round we'll do the forwarding
		*/

		if($confirmations > 0) {

			if($addr = Coinprism::get_address($deposit['address'])) {

			$btc_balance = $addr['btc_address']['balance'];

			$fee_required = DEFAULT_TRANSACTION_FEE - $btc_balance;

				//if( ($fee_required > 0) && ('BTC' != $deposit['currency']) ) {
				if( ($fee_required > 0) && (! $tx['FeeSent']) ) {

				$input = array('address' => TRANSACTION_FEE_ADDRESS, 'key' => TRANSACTION_FEE_KEY);
				$outputs[] = array('address' => $addr['btc_address']['address'], 'amount' => $fee_required);

				$fee_sent_hash = Coinprism::send($input, $outputs);
				$data['FeeSent'] = $fee_sent_hash;

				}

				elseif( ($fee_required <= 0) && (! $tx['Forwarded']) ) { //if no fee required and not already been forwarded, forward to warm wallet
						
					if('BTC' == $deposit['currency']) {	
						$asset_id = false;
						$amount = $deposit['amount'] - DEFAULT_TRANSACTION_FEE;
						$output_address = WARM_WALLET_BTC;
				
					//we set the FeeSent to NotNeeded here, otherwise subsequent notifications (after the deposit has been forwarded) will trigger a fee_required
					$data['FeeSent'] = 'NotNeeded';
		
						}
						else{
							if('TCP' == $deposit['currency']) $asset_id = TCP_ASSET_ID;
							if('DCT' == $deposit['currency']) $asset_id = DCT_ASSET_ID;
							$amount = $deposit['amount'];
							$output_address = WARM_WALLET_ASSET;
						    }
					
				if($amount > 0) {
			
					//now we need the private key
					$privkey = Addresses::find('first', array(
							'conditions' => array(
								'btc_address' => $addr['btc_address']['address']
							))
							);
					$privkey = $privkey['private_key'];
					

					$input = array('address' => $deposit['address'], 'key' => $privkey);
					$outputs[0]['address'] = $output_address;
					$outputs[0]['amount'] = $amount;

					if($asset_id) $outputs[0]['asset_id'] = $asset_id;
				  
					$forward_hash = Coinprism::send($input, $outputs);
					$data['Forwarded'] = $forward_hash;

				        }
					}
			} //get_address
		}

                         //save the transaction
                         $tx->save($data);


	} //foreach
			    
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
