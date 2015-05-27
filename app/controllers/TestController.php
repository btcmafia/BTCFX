<?php
namespace app\controllers;

class TestController extends \lithium\action\Controller {


	public function index(){
/*
		$secret = $_GET['secret'];;
		$userid = $_GET['userid']; //invoice_id is past back to the callback URL
		$invoice = $_GET['invoice'];
		$transaction_hash = $_GET['transaction_hash'];
		$input_transaction_hash = $_GET['input_transaction_hash'];
		$input_address = $_GET['input_address'];
		$value_in_satoshi = $_GET['value'];
		$value_in_btc = $value_in_satoshi / 100000000;	
		$details = Details::find('first',
			array(
					'conditions'=>array(
						'user_id'=>$userid,
						'secret'=>$secret)
				));
				if(count($details)!=0){
				$Transactions = Transactions::find('first',array(
							'conditions'=>array('TransactionHash' => $transaction_hash)
						));
					if($Transactions['_id']==""){
						$tx = Transactions::create();
						$data = array(
							'DateTime' => new \MongoDate(),
							'TransactionHash' => $transaction_hash,
							'username' => $details['username'],
							'address'=>$input_address,							
							'Amount'=> (float)number_format($value_in_btc,8),
							'Currency'=> 'BTC',						
							'Added'=>true,
						);							
						$tx->save($data);
						$dataDetails = array(
							'balance.BTC' => (float)number_format((float)$details['balance.BTC'] + (float)$value_in_btc,8),
						);
						$details = Details::find('all',
							array(
							'conditions'=>array(
								'user_id'=>$userid,
								'secret'=>$secret
							)
						))->save($dataDetails);
					}
				}
// Send email to client for payment receipt, if invoice number is present. or not
*/
			
//echo '<h1>Hello World</h1>';
//http_response_code(200);
			return  $this->render(array('layout' => false));	
	}


	public function testing($foo) {

	echo "<h1>$foo</h1>";
	return;
	}

 }
?>
