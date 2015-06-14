<?php
namespace app\controllers;

use app\models\Users;
use app\models\Details;
use app\models\Orders;
use app\models\Trades;
use app\models\Queue;
use app\controllers\ApiController;
use app\models\Transactions;
use app\extensions\action\Money;
use app\extensions\action\ActionLog;
use app\extensions\action\CallQueue;
use MongoDate;
use lithium\util\String;

class TradeController extends \app\extensions\action\Controller {


	function index() {

	return;
	}

	function x($market = null, $flag = null) {

	if('api' == $flag) { 
			$this->secure('api'); 
			$protocol = 'api';
			 } 
	else { 
		$this->secure();
		$protocol = 'web';
		}
	
	$details = $this->get_details();
	$user_id = $this->get_user_id();

	$money = new Money($user_id);

	if($this->request->query['json']==true){
                $this->_render['type'] = 'json';
        }

        if(! in_array($market, $this->get_markets()) ) {$this->redirect(array('controller'=>'in','action'=>'accounts'));}


                $first_curr = strtoupper(substr($market,0,3));
                $second_curr = strtoupper(substr($market,4,3));

		$my_first_balance = $money->get_balance($first_curr);
		$my_second_balance = $money->get_balance($second_curr);
		
		//for printing to page
		$first_balance = $money->get_balance($first_curr, true);
		$second_balance = $money->get_balance($second_curr, true);

		$my_open_first_balance = $details["OpenBalance.$first_curr"];
		$my_open_second_balance = $details["OpenBalance.$second_curr"];
   
                $title = $first_curr . " / " . $second_curr;

//echo "<p>First balance $first_curr: $my_first_balance<br/>";
//echo "Second balance $second_curr: $my_second_balance<br/>";
    
	//trade submitted by post
	if(($this->request->data)){
		$amount = $this->request->data['Amount'];
		$price = $money->undisplay_money($this->request->data['Price'], $second_curr);
		$order_value = (int) $amount * $price;
		$amount = (int) $money->undisplay_money($this->request->data['Amount'], $first_curr);
		
		//buy or sell?
		if('buy' == $this->request->data['Type']) {

			 $type = 'buy'; 
			 $opp_type = 'sell'; 
			 $sign = '<='; 
		
		
				//check balance
				if($my_second_balance < $order_value) {
		
				$error = 'Insufficient funds';
				}		 
	
		}elseif('sell' == $this->request->data['Type']) { 

			$type = 'sell'; 
			$opp_type = 'buy'; 
			$sign = '>=';

				//check balance
				if($my_first_balance < $amount) {

				$error = 'Insufficient funds';
				}
		
		} else { 

			$error = 'Invalid trade type'; 
		}



		//some defaults until implemeneted
		$min_amounts = array('BTC' => 5000000, 'TCP' => 500, 'DCT' => '750');
		$expires = 'GTC';
		$min_amount = 1; 
		$is_dark = '0';
	
			if($amount < $min_amounts[$first_curr]) {

			$error = 'The minimum amount you can buy or sell in this market is '. $money->display_money($min_amounts[$first_curr], $first_curr) . " $first_curr";
			}

				if(isset($error)) {

				if('api' == $protocol) {
					return $error;
				} 
				else{

				//get the orderbook for displaying	
				$foo = new APIController();	
				$orders = (array) $foo->orders($market, 20, true);
	
				return compact('title', 'first_curr', 'second_curr', 'first_balance', 'second_balance', 'error', 'orders'); 
				}
				}
		//add the order to the queue
	        $queue = Queue::create();

		$params = array(
				'user_id' => $user_id,
				'market' => $market,
				'amount' => (float) $this->request->data['Amount'],
				'price' => $this->request->data['Price'],
				'type' => $this->request->data['Type'],
				'expires' => $expires,
				'min_amount' => $min_amount,
				'is_dark' => $is_dark,
				'protocol' => $protocol,
				);

       		$data = array(
                        'Type' => 'place_order',
                        'DateTime' => new \MongoDate(),
                        'Params' => $params, 
                     );

        $queue->save($data);

	//TODO: Should we log the queued order?

        //let's call the queue until we find a better way to do so.
        //it's not worth a cron job yet.
        new CallQueue();

	if('api' == $protocol) return true;

	//refresh page, with success message
	return $this->redirect("/trade/x/$market/1/");
	}

	if('1' == $flag) { $message = 'Order succesfully submitted'; }


	//get the orderbook for displaying	
	$foo = new APIController();	
	$orders = (array) $foo->orders($market, 20, true);

	return compact('title', 'first_curr', 'second_curr', 'first_balance', 'second_balance', 'message', 'orders');
	}


   public function RemoveOrder($hash, $order_id, $back = ''){

	if('api' == $back) { $this->secure('api'); } 
	else { $this->secure(); }

	$user_id = $this->get_user_id();
	$details = $this->get_details();

	$order = Orders::find('first', array(
			'conditions' => array(
				'user_id' => (string) $user_id,
				'_id' => $order_id,
				)
				));

	if('api' == $back) {

	$protocol = 'api';

	if(0 == count($order)) {

	$error = 'Invalid order';
	return compact('error');
	}

	} else {

		$protocol = 'web';

		if(String::hash($order['_id']) != $hash) {

		$error = 'Invalid order';
		return compact('error');
	}
	}	

	//add the request to the queue
	$queue = Queue::create();

	$data = array(
			'Type' => 'cancel_order',
			'DateTime' => new \MongoDate(),
			'Params' => array('hash' => $hash, 'order_id' => $order_id, 'user_id' => $user_id, 'protocol' => $protocol),
		     );

	$queue->save($data);

	//let's call the queue until we find a better way to do so.
	//it's not worth a cron job yet.
	new CallQueue();

	if('api' == $back) { return true; }

	return $this->redirect('/in/orders');
	}


 } 




?>
