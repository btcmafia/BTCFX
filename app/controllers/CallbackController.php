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
                                                'Amount'=> (int) $deposit['amount'],
                                                'Currency'=> $deposit['currency'],
                                                'Status' =>$status,
                                                'TransactionType' => 'Deposit',
                                                'Added'=>false, //the deposit has not been added to balance 
                                                );

                                } else {

					//already exists, if we need to update the balance then we'll have to add it to the queue
					if( ('completed' == $status) && ($tx['Added'] == false) ) {

                                        //add to Queue
					$queue = Queue::create();

				                $queue_params = array(
			                                'user_id' => $user_id,
                        			        'currency' => $deposit['currency'],
                               				'amount' => $deposit['amount'],
							'tx_hash' => $tx_hash,
							'tx_id' => $tx['_id'],
                                			'protocol' => 'system',
                                			);

					$queue_data = array(
							'Type' => 'new_deposit',
							'DateTime' => new \MongoDate(),
							'Params' => $queue_params, 
							);

					$queue->save($queue_data);

					//status will be updated by the queue
					//$tx['Status'] => 'completed';
					$tx['Added'] = 'queued';
	
					}
					elseif($tx['Added'] != 'queued') { //just update the status
                                        	$data['Status'] = $status;
					     }
                                }

		} //foreach
		
		//if need be forward the deposit to warm wallet etc	   
		if( ($confirmations > 0) && (! isset($tx['FeeSent'])) && (! isset($tx['Forwarded'])) ) {
		$this->forward_deposit($deposit, $tx);
		}
 
			return $this->render(array('layout' => false));
	
			} //end data posted

		return;
		}


	/*
		Will either send a small amount of BTC to pay the transaction fee, or if already sent (or not required) will forward the deposit to our warm wallet
	*/
	private function forward_deposit($params, $tx) {

         if($addr = Coinprism::get_address($params['address'])) {

                        $btc_balance = $addr['btc_address']['balance'];

                        $fee_required = DEFAULT_TRANSACTION_FEE - $btc_balance;

				//send a transaction fee, we'll forward next time
                                if( ($fee_required > 0) && (! $tx['FeeSent']) ) {

                                $input = array('address' => TRANSACTION_FEE_ADDRESS, 'key' => TRANSACTION_FEE_KEY);
                                $outputs[] = array('address' => $addr['btc_address']['address'], 'amount' => $fee_required);

                                $fee_sent_hash = Coinprism::send($input, $outputs);
                                $data['FeeSent'] = $fee_sent_hash;

                                }

 				//if no fee required and not already been forwarded, forward to warm wallet
                                elseif( ($fee_required <= 0) && (! $tx['Forwarded']) ) {

                                        if('BTC' == $params['currency']) {
                                                $asset_id = false;
                                                $amount = $params['amount'] - DEFAULT_TRANSACTION_FEE;
                                                $output_address = WARM_WALLET_BTC;

                                        //we set the FeeSent to NotNeeded here, otherwise subsequent notifications (after the deposit has been forwarded) will trigger a fee_required
                                        $data['FeeSent'] = 'NotNeeded';

                                                }
                                                else{
                                                        if('TCP' == $params['currency']) $asset_id = TCP_ASSET_ID;
                                                        if('DCT' == $params['currency']) $asset_id = DCT_ASSET_ID;
                                                        $amount = $params['amount'];
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


                                        $input = array('address' => $params['address'], 'key' => $privkey);
                                        $outputs[0]['address'] = $output_address;
                                        $outputs[0]['amount'] = $amount;

                                        if($asset_id) $outputs[0]['asset_id'] = $asset_id;

                                        $forward_hash = Coinprism::send($input, $outputs);
                                        $data['Forwarded'] = $forward_hash;

                                        }
                                        }
                        } //get_address
	}

	private function convert_status($confirmations) {

	if($confirmations == 0) return 'pending';
	elseif($confirmations >= 3) return 'completed';
	elseif($confirmations >= 1) return 'confirmed';
	}
}

?>
