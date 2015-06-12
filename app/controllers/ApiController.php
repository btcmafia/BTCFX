<?php
namespace app\controllers;

use app\models\Orders;
use app\models\Trades;
use app\models\ApiRequests;
use app\extensions\action\Money;
use lithium\util\String;

class APIController extends \app\extensions\action\Controller {

	public function index() {

return 'BTC API';
//	return;
	}


	/*
	If $return then will return the result as array
	Otherwise will render it as json
	*/
	public function orders($market, $limit = false, $return = false) {

	//don't limit requests where $return is true, because it's probably the trade page 
	if(! $return) {

	//record the request
	$this->record_api('orders');
	
	if($this->limit_api()) {

		$result['error'] = array('code' => '2', 'message' => 'API request limit reached');

		return $this->render(array('json' => $result, 'status'=> 200));

	}
	}


		if(! in_array($market, $this->get_markets()) ) { 

			$result['error'] = array('code' => '0', 'message' => 'Invalid market');

			return $this->render(array('json' => $result, 'status'=> 200));
		 }

                $first_curr = strtoupper(substr($market,0,3));
                $second_curr = strtoupper(substr($market,4,3));


		$orders = Orders::find('all', array(
				'conditions' => array(

                                        '$or' => array(
                                                       array('Expires' => 'GTC'),
                                                       array('Expires' => array('>' => new \MongoDate()))
                                       ),

					'FirstCurrency' => $first_curr,
					'SecondCurrency' => $second_curr,
					'Dark' => '0'
					)
					));

		$money = new Money();

		$result['timestamp'] = time();
		$result['bids'] = array();
		$result['asks'] = array();

		foreach($orders as $order) {

			$price = $money->display_money($order['Price'], $second_curr);
			$amount = $money->display_money($order['Amount'], $first_curr);

			if('buy' == $order['Type']) $result['bids'][] = array($price, $amount);

			elseif('sell' == $order['Type']) $result['asks'][] = array($price, $amount); 

		}

//reorder
usort($result['bids'], function($a, $b){
return $a[0]<$b[0];
});

usort($result['asks'], function($a, $b){
return $a[0]>$b[0];
});

		if($limit) {

		$result['bids'] = array_slice($result['bids'], 0, $limit);
		$result['asks'] = array_slice($result['asks'], 0, $limit);
		}

		if($return) return $result;
		
		return $this->render(array('json' => $result, 'status'=> 200));
	}

	public function transactions($market, $time_limit = false, $min_amount = 1) {

	//record the request
	$this->record_api('transactions');
	
	if($this->limit_api()) {

		$result['error'] = array('code' => '2', 'message' => 'API request limit reached');

		return $this->render(array('json' => $result, 'status'=> 200));

	}

		if(! in_array($market, $this->get_markets()) ) { 

			$result['error'] = array('code' => '0', 'message' => 'Invalid market');

			return $this->render(array('json' => $result, 'status'=> 200));
		 }

                $first_curr = strtoupper(substr($market,0,3));
                $second_curr = strtoupper(substr($market,4,3));
			if(! $time_limit) $time_limit = 60 * 60;

			$time_limit = time() - $time_limit;		



		$trades = Trades::find('all', array(
				'conditions' => array(
					'FirstCurrency' => $first_curr,
					'SecondCurrency' => $second_curr,
					'Amount' => array('>=' => $min_amount),
					'DateTime' => array('>=' => $time_limit),
					),
				'sort' => array('DateTime' => 'DESC')
					));

		if(0 == count($trades)) {

		$result['error'] = array('code' => '1', 'message' => 'No results found');
		}
		
		else {
		
			$money = new Money();
			
			foreach($trades as $trade) {

			$price = $money->display_money($trade['Price'], $second_curr);
			$amount = $money->display_money($trade['Amount'], $first_curr);

			$result[] = array('date' => $trade['DateTime'], 'tid' => $trade['_id'], 'price' => $price, 'amount' => $amount);
			}
		    }
		
	
		return $this->render(array('json' => $result, 'status'=> 200));

	}


	public function cancel_order() {

	$details = $this->auth('cancel_order');	
	$user_id = $details['user_id'];

	
	
	}



	private function auth($type) {

	   if(!$this->request->data){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>"Not submitted through POST."
			)));
			die;
	 }

	$key = $this->request->data['key'];
	$nonce = $this->request->data['nonce'];
	$sig = $this->request->data['signature'];

	 if ($key==null){

		$error = "API Key not specified.";

	} elseif(! $this->check_nonce($key, $nonce) ) {

		$error = "Invalid nonce. It must always be greater than the previous.";

	 }else{
	 
	 	$details = Details::find('first',array(
			'conditions'=>array('API.Key'=>$key)
		));

		if(count($details)==0){  $error = "Invalid API key."; }

		else{

		//check signature
		if(string::hash($key . $details['API.secret'] . $nonce) != $sig) {
		$error = "Invalid signature";
		}

		if(! $this->limit_api() ) {
		$error = "Too many requests from your IP, please try again after some time.";
		}


		}

	if(isset($error)) {

		$this->record_api($type, $key, $nonce, 'failed');
		
		return $this->render(array('json' => array('success'=>0,
			'timestamp' => time(),
			'error' => $error
			)));
			die;
	}

		//if not failed by now we must be good
		$this->record_api($type, $key, $nonce, 'success');

		return $details;
	}
	}

	private function check_nonce($key, $nonce) {

		$foo = ApiRequests::find('first', array(
				'conditions' => array(
					'Key' => $key,
					'Nonce' => array('>=' => $nonce),
				)
				));

		if(0 != count($foo) ) return false;

		else return true;
	}

	private function record_api($type, $api_key = false, $nonce = '', $result = '') {

		$record = ApiRequests::create();

			$data = array(
				'Type' => $type,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'Timestamp' => time(),
			);
		
		if(isset($api_key)) {

			$data['Key'] = $api_key;
			$data['Nonce'] = $nonce;
			$data['Result'] = $result;
		}
		
		$record->save($data);
	}
	
	/*
		@param $time_limit (int) - number of seconds the $limit is enforced for, default 600, or 10 min
		@return bool
	*/
	private function limit_api($limit = 600, $time_limit = 600) {


		$requests = ApiRequests::find('count', array(
					'conditions' => array(
						'ip_address' => $_SERVER['Remote_ADDR'],
						'Timestamp' => array('>=' => $time_limit),
						)
					));

		if($requests >= $limit) return false;

		else return true;
	}

}
?>
