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

class NewTradeController extends \app\extensions\action\Controller {


	function index() {

	return;
	}

	function x($market = null, $flag = null) {

	$this->secure();
	$details = $this->get_details();
	$user_id = $this->get_user_id();

	$money = new Money($user_id);
	//$action = new ActionLogs();

	if($this->request->query['json']==true){
                $this->_render['type'] = 'json';
        }

        if(! in_array($market, $this->get_markets()) ) {$this->redirect(array('controller'=>'in','action'=>'accounts'));}


                $first_curr = strtoupper(substr($market,0,3));
                $second_curr = strtoupper(substr($market,4,3));

		$my_first_balance = $money->get_balance($first_curr);
		$my_second_balance = $money->get_balance($second_curr);
		
		//for printing to page in case of error
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
		$my_price = (int)$money->undisplay_money($this->request->data['Price'], $second_curr);
		$order_value = (int) $amount * $my_price;
		
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



//echo "Amount: $amount<br />";
//echo "Price: $my_price<br /></p>";

		//some defaults until implemeneted
		$min_amounts = array('BTC' => 5000000, 'TCP' => 500, 'DCT' => '750');
		$my_expires = 'GTC';
		$min_amount = 1; 
		$is_dark = '0';
		$protocol = 'web';
	
			if($amount < $min_amounts[$first_curr]) {

			$error = 'The minimum amount you can buy or sell in this market is '. $money->display_money($min_amounts[$first_curr], $first_curr) . " $first_curr";
			}

		if(isset($error)) return compact('title', 'first_curr', 'second_curr', 'first_balance', 'second_balance', 'error'); 


		//create a new order, so we have an order_id for transactions and the action log. We can delete it if the order is completely fulfilled from pending orders
		$new_order = Orders::create();
		$new_order->save();
		$new_order_id = $new_order['_id']; 

		
		//log the action
		$log = new ActionLog();
		$log->order($user_id, $market, $new_order_id, $type, $amount, $my_price, $my_expires, $min_amount, $is_dark, $protocol);

		$orders = Orders::find('all', array(
				'conditions' => array(
					
					'$or' => array(
                                                       array('Expires' => 'GTC'),
                                                       array('Expires' => array('>' => new \MongoDate()))
                                       ),

					'FirstCurrency' => $first_curr,
					'SecondCurrency' => $second_curr,
					'Type' => $opp_type,
					'MinAmount' => array('<=' => $amount),
					'Price' => array($sign => $my_price),
					), 
				
	
					'order'=>array('DateTime'=>-1)		
					));

//echo "<p>I want to $type $amount $second_curr for $my_price</p>";

		foreach($orders as $order) {

//echo "<p>Found someone who wants to $opp_type {$order['Amount']} for {$order['Price']} </p>";
//print_r($order['Price']);

		//oops!
		$order['order_id'] = $order['_id'];

			if($amount < $order['Amount']) {

					$order_amount = $amount;
					$foo = $amount; //display_money $amount is passed by reference, we don't actually want to change it here
					$order_value = $money->display_money($foo, $order['FirstCurrency']) * $order['Price'];
					$new_order_amount = $order['Amount'] - $order_amount;
			
			} else {

					$order_amount = $order['Amount'];
					$foo = $order['Amount'];
					$order_value = $money->display_money($foo, $order['FirstCurrency']) * $order['Price'];
			}

				//the other persons details
		                $other = Details::find('first', array(
	                                   'conditions' => array(
                                                'user_id' => $order['user_id'])
                                                ));
/*
echo "<p>Order Amount: $order_amount</p>";
echo "<p>Order Value: $order_value</p>";
die;
*/
                                //calculate the other persons new balances and commissions payable
                      
			        $first_curr_commission = $order_amount * $other['Commission'] / 10000; 
                                $second_curr_commission = $order_value * $other['Commission'] / 10000;


                                        if('buy' == $opp_type) {


                                                $other_first_balance = $other["balance.$first_curr"] + $order_amount - $first_curr_commission;
						$other_open_second_balance = $other["OpenBalance.$second_curr"] - $order_value; 

                                $this->record_transaction($order['user_id'], $order['order_id'], $second_curr, $first_curr, 'sell', $order_value, $order_amount);
                                $this->record_transaction($order['user_id'], $order['order_id'], $first_curr, $second_curr, 'buy', $order_amount, $order_value, $first_curr_commission);

                                                $commissions[$first_curr][] = $first_curr_commission;

                                		$other_data = array("balance.$first_curr" => (int) $other_first_balance,
                                                    		    "OpenBalance.$second_curr" => (int) $other_open_second_balance,
                                                	);

                                        } else {


						$other_open_first_balance = $other["OpenBalance.$first_curr"] - $order_amount; 
                                                $other_second_balance = $other["balance.$second_curr"] + $order_value - $second_currency_commission;


                           $this->record_transaction($order['user_id'], $order['order_id'], $first_curr, $second_curr, 'sell', $order_amount, $order_value);
                           $this->record_transaction($order['user_id'], $order['order_id'], $second_curr, $first_curr, 'buy', $order_value, $order_amount, $second_curr_commission);

                                                $commissions[$second_curr][] = $second_curr_commission;

						$other_data = array(
                                                    		    "OpenBalance.$first_curr" => (int) $other_open_first_balance,
                                                    		    "balance.$second_curr" => (int) $other_second_balance,
                                                	);

                                        }

                                //save the other balances
                                $other->save($other_data);

			

			if($amount < $order['Amount']) {

				$order_data = array(
						'Amount' => (int) $new_order_amount,
						);
				
				//update the other order amount
				  Orders::find('first', array(
                                        'conditions' => array(
                                                '_id' => $order['order_id'])
                                        ))->save($order_data);

			} else {

//print_r($order['order_id']);
//die;
				//delete the order - it's completed
                                Orders::find('first', array(
                                        'conditions' => array(
                                                '_id' => $order['order_id'])
                                        ))->delete();

			}

		//Now update our balances and order
 
			      $my_first_curr_commission = $order_amount * $details['Commission'] / 10000;
                              $my_second_curr_commission = $order_value * $details['Commission'] / 10000;


                                        if('buy' == $type) {


                                                $my_first_balance = $my_first_balance + $order_amount - $my_first_curr_commission;
                                                $my_second_balance = $my_second_balance - $order_value;

                                      $this->record_transaction($user_id, $new_order_id, $first_curr, $second_curr, 'buy', $order_amount, $order_value, $my_first_curr_commission);
                                      $this->record_transaction($user_id, $new_order_id, $second_curr, $first_curr, 'sell', $order_value, $order_amount);

                                                $commissions[$first_curr][] = $first_curr_commission;

                                        } else {

                                                $my_first_balance = $my_first_balance - $order_amount;
                                                $my_second_balance = $my_second_balance + $order_value - $my_second_curr_commission;
                                               
                                      $this->record_transaction($user_id, $new_order_id, $second_curr, $first_curr, 'buy', $order_value, $order_amount, $my_second_curr_commission);
                                      $this->record_transaction($user_id, $new_order_id, $first_curr, $second_curr, 'sell', $order_amount, $order_value);

                                                $commissions[$second_curr][] = $second_curr_commission;
                                        }


			//record the transaction in the public trades collection
			$trade = Trades::create();

			$trade_data = array(
						'DateTime' => time(),
						'FirstCurrency' => $first_curr,
						'SecondCurrency' => $second_curr,
						'Price' => $order_value,
						'Amount' => $order_amount,
					);

			$trade->save($trade_data);

			if($amount < $order['Amount']) {
                        
			$amount = 0;

			break;

			} else {

                        $amount = $amount - $order['Amount'];
			
			}

                        if(0 == $amount) break;

		} //end foreach

//echo "<p>That's all the orders, now the amount is $amount</p>";
//die;
	//if our order is satisfied delete it, otherwise save what remains
	if(0 == $amount) {

	$new_order->delete();
	
	//update balances here
		$data = array(	
				"balance.$first_curr" => (int) $my_first_balance,
				"OpenBalance.$first_curr" => (int) $my_open_first_balance,
				"balance.$second_curr" => (int) $my_second_balance,
				"OpenBalance.$second_curr" => (int) $my_open_second_balance,
				);
	
	}
	else {	
		$data = array(
				'user_id' => $user_id,
				'FirstCurrency' => $first_curr,
				'SecondCurrency' => $second_curr,
				'Type' => $type,
				'Amount' => $amount,
				'Price' => $my_price,
				'MinAmount' => $min_amount,
				'Expires' => $my_expires,
				'Dark' => $is_dark,
				'DateTime' => new \MongoDate(),
			);

		$new_order->save($data);

	if('buy' == $type) {

		$amount = $money->display_money($amount, $first_curr);

//echo "<p>My Open second: $my_open_second_balance</p>";
//echo "<p>New amount: $amount $first_curr</p>";

		//take from balance and add to OpenOrders balance
		$my_second_balance = $my_second_balance - ($amount * $my_price);
		$my_open_second_balance = $my_open_second_balance + ($amount * $my_price);
		
		$data = array(
				"balance.$second_curr" => (int) $my_second_balance,
				"OpenBalance.$second_curr" => (int) $my_open_second_balance,

				);
//print_r($data);
//die;
	} else {

		$my_first_balance = $my_first_balance - $amount;
		$my_open_first_balance = $my_open_first_balance + $amount;

		$data = array(
				"balance.$first_curr" => (int) $my_first_balance,
				"OpenBalance.$first_curr" => (int) $my_open_first_balance,
				
				);
	}

	    }


		$details->save($data);
		//Details::find('first', array(
		//	'conditions' => array('user_id' => $user_id)))->save($data);


	//refresh page, with success message
	return $this->redirect("/new_trade/x/$market/1/");

	} //trade submitted		

	if('1' == $flag) { $message = 'Order succesfully placed'; }

	$first_balance = $money->display_money($my_first_balance, $first_curr);
	$second_balance = $money->display_money($my_second_balance, $second_curr);

	//get the orderbook for displaying	
	$foo = new APIController();	

	$orders = (array) $foo->orders($market, 20, true);

	return compact('title', 'first_curr', 'second_curr', 'first_balance', 'second_balance', 'message', 'orders');
	}


   public function RemoveOrder($hash,$order_id,$back){

	$this->secure();
	$user_id = $this->get_user_id();
	$details = $this->get_details();

	$protocol = 'web'; //api not done yet

	$order = Orders::find('first', array(
			'conditions' => array(
				'user_id' => (string) $user_id,
				'_id' => $order_id,
				)
				));

	if(String::hash($order['_id']) != $hash) {

	$error = 'Invalid order';
	return compact('error');
	}

	//add the request to the queue
	$queue = Queue::create();

	$data = array(
			'Type' => 'remove_order',
			'DateTime' => new \MongoDate(),
			'Params' => array('hash' => $hash, 'order_id' => $order_id, 'user_id' => $user_id, 'protocol' => $protocol),
		     );

	$queue->save($data);

	//let's call the queue until we find a better way to do so.
	//it's not worth a cron job yet.

	new CallQueue();


	return $this->redirect('/in/orders');
	//this will be the end.

	$money = new Money();

		if('buy' == $order['Type']) {

		$current_balance = $details["balance.{$order['SecondCurrency']}"];
		$current_open_balance = $details["OpenBalance.{$order['SecondCurrency']}"];
		
		$amount = $money->display_money($order['Amount'], $order['FirstCurrency']);
		$price = $order['Price']; 

		$order_value = $amount * $price;

		$new_open_balance = $current_open_balance - $order_value;
		$new_balance = $current_balance + $order_value;


		$data = array("OpenBalance.{$order['SecondCurrency']}" => (int) $new_open_balance,
				"balance.{$order['SecondCurrency']}" => (int) $new_balance);
		}

		elseif('sell' == $order['Type']) {

		$current_balance = $details["balance.{$order['FirstCurrency']}"];
		$current_open_balance = $details["OpenBalance.{$order['FirstCurrency']}"];
		
		//$amount = $money->display_money($order['Amount'], $order['FirstCurrency']);
		$amount = $order['Amount'];

		$new_open_balance = $current_open_balance - $amount;
		$new_balance = $current_balance + $amount;
	
		$data = array("OpenBalance.{$order['FirstCurrency']}" => (int) $new_open_balance,
				"balance.{$order['FirstCurrency']}" => (int) $new_balance);

		}
		else { return false; } //should be impossible!

	//delete order and update balances
	$details->save($data); 
	$order->delete();
	
	$message = 'Order deleted';
        
	$market = strtolower("{$order['FirstCurrency']}_{$order['SecondCurrency']}");

		//log the action
		$log = new ActionLog();
		$log->order_cancelled($user_id, $market, $order_id, $protocol);

	return $this->redirect('/in/orders');
      
	  }

        // $this->record_transaction($user_id, $new_order_id, $first_curr, $second_curr, 'sell', $order_value, $order_amount);
        // $this->record_transaction($user_id, $new_order_id, $second_curr, $first_curr, 'buy', $order_amount, $order_value);

 private function record_transaction($user_id, $order_id, $first_curr, $second_curr, $type, $amount, $price, $commission = 0) {

	$tx = Transactions::create();

	$type = ucfirst($type);

	if('Sell' == $type) $amount = $amount * -1; //when you sell it's recorded as negative


	/*
		We don't really care about the price or SecondCurrency here, as each currency is recorded seperately 
		But we record them so user can click on a transaction and see the full picture, i.e. the market the transaction occurred on and the price paid.
	*/

	         $data = array(
                             'DateTime' => new \MongoDate(),
                             'order_id' => $order_id,
                             'user_id' => $user_id,
                             'Amount'=> (int) $amount,
			     'Price' => (int) $price, 
                             'Currency'=> $first_curr,
          		     'SecondCurrency' => $second_curr,
			     'Commssion' => (int) $commission,
	                     'Status' => 'completed',
                             'TransactionType' => $type,
                             'Added'=> true,
                            );

	$tx->save($data);


 return;
 } 



}

?>
