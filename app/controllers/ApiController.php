<?php
namespace app\controllers;

use app\models\Orders;
use app\models\Trades;
use app\models\Details;
use app\models\ApiRequests;
use app\extensions\action\Money;
use app\extensions\action\Coinprism;
use app\controllers\InController;
use app\controllers\TradeController;
use lithium\util\String;

class APIController extends \app\extensions\action\Controller {

	public function index() {

	return 'BTC API';
	}


	/*
	If $return then will return the result as array
	Otherwise will render it as json

	@TODO: Should properly formed requests that fail for another reason return a response code other than 200?
	*/
	public function orders($market, $limit = false, $return = false) {

	//don't record / limit requests where $return is true, because it's probably the trade page 
	if(! $return) {

	//record the request
	$this->record_api('orders');
	
	if($this->limit_api()) {

		$result['error'] = array('code' => '2', 'message' => 'API request limit reached');

		echo $this->render(array('json' => $result, 'status'=> 200));
		die;
	}
	}


		if(! in_array($market, $this->get_markets()) ) { 

			$result['error'] = array('code' => '0', 'message' => 'Invalid market');

			echo $this->render(array('json' => $result, 'status'=> 200));
			die;
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
		
		echo $this->render(array('json' => $result, 'status'=> 200));
		die;
	}

	public function transactions($market, $time_limit = false, $min_amount = 1) {

	//record the request
	$this->record_api('transactions');
	
	if($this->limit_api()) {

		$result['error'] = array('code' => '2', 'message' => 'API request limit reached');

		echo $this->render(array('json' => $result, 'status'=> 200));
		die;
	}

		if(! in_array($market, $this->get_markets()) ) { 

			$result['error'] = array('code' => '0', 'message' => 'Invalid market');

			echo $this->render(array('json' => $result, 'status'=> 200));
			die;
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

			$result[] = array('date' => $trade['DateTime'], 'tid' => (string) $trade['_id'], 'price' => $price, 'amount' => $amount);
			}
		    }
		
	
		echo $this->render(array('json' => $result, 'status'=> 200));
		die;
	}


	public function balances() {

	$details = $this->auth('balances');
	$user_id = $details['user_id'];

	$time = time();

	$money = new Money($user_id);
	
	$balances = $money->get_balances();

	$result = array('success' => 1, 'timestamp' => $time, 'balances' => $balances);

	echo $this->render(array('json' => $result, 'status' => 200));
	die;
	}


	public function user_transactions() {

	$details = $this->auth('user_transactions');
	
	$time = time();

		$foo = new InController();
		$trans = (array) $foo->transactions('api', $details);

	$result = array('success' => 1, 'timestamp' => $time, 'transactions' => $trans);

	echo $this->render(array('json' => $result, 'status' => 200));
	die;
	}



	public function open_orders() {

	$details = $this->auth('open_orders');
	$time = time();

		$foo = new InController();
		$orders = $foo->orders('api', $details);

	$result = array('success' => 1, 'timestamp' => $time, 'orders' => $orders);

	echo $this->render(array('json' => $result, 'status' => 200));
	die;
	}



	public function place_order() {

	$this->auth('place_order');

	$market = $this->request->data['Market'];

	if(! in_array($market, $this->get_markets()) ) { 

		//failed even before security, better record it
		$error = 'Invalid market'; 
		$this->record_api($type, $this->request->data['key'], $this->nonce->data['nonce'], 'failed');
		return $this->render(array('json' => array('success' => 0, 'timestamp' => time(), 'error' => $error), 'status' => 200));
		}

	$x = new TradeController();
	$result = $x->x($market, 'api');
	}	

	public function cancel_order() {

		$this->auth('cancel_order');
		
		$foo = new TradeController();
		$result = $foo->RemoveOrder('', $this->request->data['order_id'], 'api');		

		if(isset($result['error'])) $result = array('success' => 0, 'timestamp' => time(), 'error' => $result['error']);

		else $result = $result = array('success' => 1, 'timestamp' => time(), 'message' => 'Order queued for deletion');

		return $this->render(array('json' => $result, 'status' => 200));	
	}


	public function cancel_all_orders() {

	//processed all here because this option is not available to web users

	$details = $this->auth('cancel_all_orders');
	$user_id = $this->get_user_id();

	$time = time();

	//make sure we have at least one order to cancel
	$order = Orders::find('first', array(
                        'conditions' => array(
                                'user_id' => (string) $user_id,
                                )
                                ));
	
		//Should this really be an error? The result is no open orders, which is what they want
		if(0 == count($order)) {	
		$error = "No orders to cancel";
		$result = array('success' => 0, 'timestamp' => $time, 'error' => $error);
		return $this->render(array('json' => $result, 'status' => 200));
		}

	//not yet implemented!

		$error = "cancel_all_orders is not yet in use.";
		$result = array('success' => 0, 'timestamp' => $time, 'error' => $error);
		return $this->render(array('json' => $result, 'status' => 200));


	/*
	 //add the request to the queue
        $queue = Queue::create();

	$datetime = new \MongoDate();
	$time = $datetime->sec;

        $data = array(
                        'Type' => 'cancel_all_orders',
                        'DateTime' => $datetime,
                        'Params' => array('user_id' => $user_id, 'protocol' => 'api'),
                     );

        $queue->save($data);

	//still calling the queue on each request
        new CallQueue();

		$message = "All open orders have been submitted for cancellation";
		$result = array('success' => 1, 'timestamp' => $time, 'message' => $message);
		return $this->render(array('json' => $result, 'status' => 200));
	*/
	}
	
	public function new_address() {

		$this->auth('new_address');
		$user_id = $this->get_user_id();

		$coinprism = new Coinprism( COINPRISM_USERNAME, COINPRISM_PASSWORD );
                $new_addresses = $coinprism->create_address($user_id);


		    if(! isset($new_addresses['error']) ) {

			$result = array('success' => 1, 'timestamp' => time(), 'addresses' => $new_addresses);
			}
                        
			else {
				
				$error = $new_addresses['error'];
			   
				$result = array('success' => 0, 'timestamp' => time(), 'error' => $error);
			      }

		return $this->render(array('json' => $result, 'status' => 200));
	}


	public function auth($type) {

	$key = $this->request->data['key'];
	$nonce = $this->request->data['nonce'];
	$sig = $this->request->data['signature'];



	if($this->limit_api() ) {
		$error = "Too many requests from your IP, please try again after some time.";
		}

	   
	elseif(!$this->request->data){ 
		$error = "Not submitted through POST.";
	 }


	 elseif ($key==null){

		$error = "API Key not specified.";

	} elseif(! $this->check_nonce($key, $nonce) ) {

		$error = "Invalid nonce. It must always be greater than the previous.";

	 }else{
	 
	 	$details = Details::find('first',array(
			'conditions'=>array('API.Key'=>$key)
		));

		if(count($details)==0){  $error = "Invalid API key."; }

		elseif('1' != $details['API.Enabled']) { $error = "API disabled"; } //too much info?

		else{

			//check signature
			if(string::hash($key . $details['API.Secret'] . $nonce, array('key' => $key)) != $sig) {
			$error = "Invalid signature";
			}


		}

	}

	if(isset($error)) {

		$this->record_api($type, $key, $nonce, 'failed');
		
		echo $this->render(array('json' => array('success'=>0,
			'timestamp' => time(),
			'error' => $error
			)));
			die;
	}

		//if not failed by now we must be good
		$this->record_api($type, $key, $nonce, 'success');

		//not needed, if these are required they'll get called later
		//$this->secure('api', $details); //make the normal security functions available, but bypass the security checks

		return $details;
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
						'ip_address' => $_SERVER['REMOTE_ADDR'],
						'Timestamp' => array('>=' => $time_limit),
						)
					));

		if(count($requests) >= $limit) return true; //true because they must be limited

		else return false;
	}

}
?>
