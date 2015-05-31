<?php
namespace app\controllers;

use app\models\Users;
use app\models\Details;
use app\models\Orders;
use app\models\Transactions;
use app\extensions\action\Money;
use app\extension\action\ActionLog;


class TradeController extends \app\extensions\action\Controller {


	function index() {

	}

	function x($market = null, $flag = null) {

	$this->secure();
	$details = $this->get_details();
	$user_id = $this->get_user_id();

	$money = new Money();
	$action = new ActionLog();

	if($this->request->query['json']==true){
                $this->_render['type'] = 'json';
        }


        if(! in_array($market, $this->get_markets()) ) {$this->redirect(array('controller'=>'in','action'=>'accounts'));}


                $first_curr = strtoupper(substr($market,0,3));
                $second_curr = strtoupper(substr($market,4,3));
   
                $title = $first_curr . " / " . $second_curr;
    
	//trade submitted by post
	if(($this->request->data)){

		//buy or sell?
		if('buy' == $this->request->data['Type']) { $type = 'buy'; $opp_type = 'sell'; }
		elseif('sell' == $this->request->data['Type']) { $type = 'sell'; $opp_type = 'buy'; }
		else { $error = 'Invalid trade type'; return compact('error'); }


		$my_price = (int) $this->request->data['Price'];
		$amount = $money->undisplay_money($this->request->data['Amount'], $first_curr);


		//some defaults until implemeneted
		$min_amounts = array('BTC' => 5000000, 'TCP' => 500, 'DCT' => '750')
		$my_expires = 'GTC';
		$min_amount = 1; 
		$is_dark = '0';
		$protocol = 'web';
	
			if($amount < $min_amounts[$first_curr]) {

			$error = 'The minimum amount you can buy or sell is this market is '. $money->display_money($min_amount, $first_curr) . " $first_curr";
			return compact('title', 'first_curr', 'second_curr', 'error'); 
			}

		//create a new order, so we have an order_id for transactions and the action log. We can delete it if the order is completely fulfilled from pending orders
		$new_order = Orders::create();
		$new_order_id = $new_order['_id']; //test this

		//log the action
		$action->order($user_id, $market, $new_order_id, $type, $amount, $my_price, $protocol);

		$orders = Orders::find('all', array(
				'conditions' => array(
					
					'$or' => array(
                                                       array('Expires' => 'GTC'),
                                                       array('Expires' => array('>' => new MongoDate()))
                                       ),

					'FirstCurrency' => $first_curr,
					'SecondCurrency' => $second_curr,
					'Type' => $opp_type,
					'MinAmount' => array('<=' => $amount),
					), 
					
					'order'=>array('DateTime'=>-1)		
					));

		$my_first_balance = $details["balance.$first_curr"];
		$my_second_balance = $details["balance.$second_curr"];

		foreach($orders as $order) {


			if($amount < $order['Amount']) {

					$order_amount = $amount;
					$order_price = $amount / $order['Amount'] * $order['Price'];

			} else {

					$order_amount = $order['Amount'];
					$order_price = $order['Amount'] * $order['Price'];
			}

				//the other persons details
		                $other = Details::find('first', array(
	                                   'conditions' => array(
                                                'user_id' => $order['user_id'])
                                                ));



                                //calculate the other persons new balances and commissions payable
                      
			        $first_curr_commission = $order_amount * $other['Commission'] / 10000; 
                                $second_curr_commission = $order_price * $other['Commission'] / 10000;


                                        if('buy' == $opp_type) {


                                                $other_first_balance = $other["balance.$first_curr"] + $order_amount - $first_curr_commission;
                                                $other_second_balance = $other["balance.$second_curr"] - $order_price;

                                                $this->record_transaction($order['user_id'], $order['order_id'], $first_curr, $second_curr, 'buy', $order_amount, $order_price);
                                                $this->record_transaction($order['user_id'], $order['order_id'], $second_curr, $first_curr, 'sell', $order_price, $order_amount);

                                                $commissions[$first_curr][] = $first_curr_commission;

                                        } else {


                                                $other_first_balance = $other["balance.$first_curr"] - $order_amount;
                                                $other_second_balance = $other["balance.$second_curr"] + $order_price - $second_curr_commission;

                                                $this->record_transaction($order['user_id'], $order['order_id'], $first_curr, $second_curr, 'sell', $order_price, $order_amount);
                                                $this->record_transaction($order['user_id'], $order['order_id'], $second_curr, $first_curr, 'buy', $order_amount, $order_price);

                                                $commissions[$second_curr][] = $second_curr_commission;
                                        }

                                $other_data = array("balance.$first_curr" => $other_first_balance,
                                                    "balance.$second_curr" => $other_second_balance,
                                                );

                                //save the other balances
                                $other->save($other_data);

			

			if($amount < $order['Amount']) {

				$order_data = array(
						'Amount' => $new_order_amount,
						);

				//update the other order amount
				  Orders::find('first', array(
                                        'conditions' => array(
                                                'order_id' => $order['order_id'])
                                        ))->save($order_data);

			} else {

				//delete the order - it's completed
                                Orders::find('first', array(
                                        'conditions' => array(
                                                'order_id' => $order['order_id'])
                                        ))->delete();

			}

		//Now update our balances and order
 
			      $my_first_curr_commission = $order_amount * $details['Commission'] / 10000;
                              $my_second_curr_commission = $order_price * $details['Commission'] / 10000;


                                        if('buy' == $type) {


                                                $my_first_balance = $my_first_balance + $order_amount - $my_first_curr_commission;
                                                $my_second_balance = $my_second_balance - $order_price;

                                                $this->record_transaction($user_id, $new_order_id, $first_curr, $second_curr, 'buy', $order_amount, $order_price);
                                                $this->record_transaction($user_id, $new_order_id, $second_curr, $first_curr, 'sell', $order_price, $order_amount);

                                                $commissions[$first_curr][] = $first_curr_commission;

                                        } else {


                                                $my_first_balance = $my_first_balance - $order_amount;
                                                $my_second_balance = $my_second_balance + $order_price - $my_second_curr_commission;

                                                $this->record_transaction($user_id, $new_order_id, $first_curr, $second_curr, 'sell', $order_price, $order_amount);
                                                $this->record_transaction($user_id, $new_order_id, $second_curr, $first_curr, 'buy', $order_amount, $order_price);

                                                $commissions[$second_curr][] = $second_curr_commission;
                                        }


			if($amount < $order['Amount']) {
                        
			$amount = 0;

			break;

			} else {

                        $amount = $amount - $order['Amount'];
			
			}

                        if(0 == $amount) break;

		} //end foreach

	//if our order is satisfied delete it, otherwise save what remains
	if(0 == $amount) {

	$new_order->delete();
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
			);

		$new_order->save($data);
	    }

	//refresh page, with success message
	return $this->redirect("/trade/x/$market/1/");

	} //trade submitted		

	if('1' == $flag) { $message = 'Order succesfully placed'; }

	return compact('title', 'first_curr', 'second_curr', 'message');
	}


 private function record_transaction($user_id, $order_id, $first_curr, $second_curr, $type, $amount, $price) {

	$tx = Transactions::create();

	$type = ucfirst($type);

	         $data = array(
                             'DateTime' => new \MongoDate(),
                             'order_id' => $order_id,
                             'user_id' => $user_id,
                             'Amount'=> $amount,
			     'Price' => $price . " $second_curr", 
                             'Currency'=> $first_curr,
                             'Status' => 'completed',
                             'TransactionType' => $type,
                             'Added'=> true,
                            );

	$tx->save($data);


 return;
 } 
}

?>
