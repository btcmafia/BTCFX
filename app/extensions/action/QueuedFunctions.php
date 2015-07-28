<?php

namespace app\extensions\action;

use app\models\Queue;
use app\models\Details;
use app\models\Orders;
use app\models\Trades;
use app\models\Transactions;
use app\models\Parameters;
use app\extensions\action\ActionLog;
use app\extensions\action\Money;
use lithium\util\String;

class QueuedFunctions extends \lithium\action\Controller{

 //var bool - if not live then DO NOT proceed, we don't have a lock on the db!
 public $live;

	public function __construct() {

		//see if the queue is locked
		$check = Parameters::find('first');

		$time_out = time() - 30; //if the last lock was more than x seconds ago, a script probably crashed, so treat it as unlocked.


		if( ('1' == $check['Queue.Locked']) && ($check['Queue.DateTime']->sec > $time_out) ) {

		$this->live = false; //already locked, so we can't create live session

		return false;
		}
		//not locked, then lock it.
		else {
			$check->save(array('Queue' => array('Locked' => '1', 'DateTime' => new \MongoDate())));
			
			$this->live = true;

		return true;
		}
					
	}

	public function get_queue() {

		$foo = Queue::find('all',array(
				'order' => array('DateTime' => 'ASC')
			));

	return $foo;
	}

	public function delete_from_queue($queue_id) {

		$foo = Queue::find('first', array(
				'conditions' => array(
					'_id' => $queue_id,
				)
			));
		
		$foo->delete();
	}


	
	public function place_order($params) {

	$user_id = $params['user_id'];
        $details = $this->get_details($user_id);

	$market = $params['market'];

        $money = new Money($user_id);


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


                $amount = $params['amount'];
                $my_price = (int)$money->undisplay_money($params['price'], $second_curr);
                $order_value = (int) $amount * $my_price;

                $amount = (int) $money->undisplay_money($params['amount'], $first_curr);

		$min_amount = $params['min_amount'];

                //buy or sell?
                if('buy' == $params['type']) {

                         $type = 'buy';
                         $opp_type = 'sell';
                         $sign = '<=';


                                //check balance
                		//
				//We already checked the balance before it was queued, but it's possible another order has changed it since then
		                if($my_second_balance < $order_value) {

                                $error = 'Insufficient funds';
				$my_failed_balance = $my_second_balance;
				$my_failed_curr = $second_curr;

                                }

                }elseif('sell' == $params['type']) {

                        $type = 'sell';
                        $opp_type = 'buy';
                        $sign = '>=';

                                //check balance
                                if($my_first_balance < $amount) {

                                $error = 'Insufficient funds';
				$my_failed_balance = $my_first_balance;
				$my_failed_curr = $first_curr;
                                }

                }


		//if error then log the rejected order and then stop
		if(isset($error)) {
		$log = new ActionLog();
		$log->order_rejected($user_id, $market, $error, $type, $amount, $my_price, $first_curr, $second_curr, $my_failed_balance, $my_failed_curr, $protocol);		
		return;
		}

                $my_expires = $params['expires'];
                $is_dark = $params['is_dark'];
                $protocol = $params['protocol'];

                
		//create a new order, so we have an order_id for transactions and the action log. We can delete it if the order is completely fulfilled from pending orders
                $new_order = Orders::create();
                $new_order->save();
                $new_order_id = $new_order['_id'];


                //log the action
                $log = new ActionLog();
                $log->order($user_id, $market, $new_order_id, $type, $amount, $my_price, $my_expires, $min_amount, $is_dark, $protocol);


		//get matching counter offers
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


                foreach($orders as $order) {

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

        //if our order is satisfied delete it, otherwise save what remains
        if(0 == $amount) {

        $new_order->delete();

        //update our balances 
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

                //take from balance and add to OpenOrders balance
                $my_second_balance = $my_second_balance - ($amount * $my_price);
                $my_open_second_balance = $my_open_second_balance + ($amount * $my_price);

                $data = array(
                                "balance.$second_curr" => (int) $my_second_balance,
                                "OpenBalance.$second_curr" => (int) $my_open_second_balance,

                                );
        
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


        return; //done

	}



	public function cancel_order($params) {


	if(! $this->live) return false;

	$user_id = $params['user_id'];
	$order_id = $params['order_id'];
	$hash = $params['hash'];
	$protocol = $params['protocol'];


	//this was already checked before the order was queued, but hey.
        if(String::hash($order_id) != $hash) {

        $error = array('error' => 'Invalid order');
        return compact('error');
        }
        
	$details = $this->get_details($user_id);

        $order = Orders::find('first', array(
                        'conditions' => array(
                                'user_id' => (string) $user_id,
                                '_id' => $order_id,
                                )
                                ));

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

        $market = strtolower("{$order['FirstCurrency']}_{$order['SecondCurrency']}");

                //log the action
                $log = new ActionLog();
                $log->order_cancelled($user_id, $market, $order_id, $protocol);

        return; 

	}


	public function new_deposit($params) {

	//new deposits are only queued when completed, because we need to update the balance
	//this means the transaction should already exist
	//so, get the transaction, update the status, update the balance

		$tx = Transactions::find('first', array(
                             'conditions' => array('_id' => $params['tx_id'], 'TransactionHash' => $params['tx_hash'], 'Address' => $params['address'], 'Currency' => $params['currency'], 'Amount' => (int) $amount['amount'], 'Added' => $params['queued'])
                        ));

		if(0 != count($tx)) {

                    $money = new Money($params['user_id']);
                    if($money->update_balance($params['amount'], $params['currency'])) {

                    	 $data['Status'] = 'completed';
			 $data['Added'] = true;

		    $tx->save($data);
                    }

		}

		//There is no ActionLog with new deposits because it is recorded as a transaction, which is complementary to the ActionLog
	
		return;
	}





	public function release_lock() {

		$this->live = false;

		$foo = Parameters::find('all');
		$foo->save(array('Queue' => array('Locked' => 0, 'DateTime' => '')));

	return;
	}


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



	private function get_details($user_id) {

		$details = Details::find('first', array(
				'conditions' => array(
					'user_id' => $user_id)
				));
		
		if(0 == count($details)) return false;

		else return $details;
	}

}

?>
