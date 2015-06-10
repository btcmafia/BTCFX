<?php

namespace app\extensions\action;

use app\models\Queue;
use app\models\Details;
use app\models\Orders;
use app\models\Parameters;
use app\extensions\actionActionLog;
use app\extensions\action\Money;
use lithium\util\String;

class QueuedFunctions extends \lithium\action\Controller{

 //var bool - if not live then DO NOT proceed, we don't have a lock on the db!
 public $live;

	public function __construct() {

		//see if the queue is locked
		$check = Parameters::find('first');

		$time_out = time() - 30; //if the last lock was more than x seconds ago, a script probably crashed, so treat it as unlocked.

//echo "<p>Time: " . $time_out;
//echo "<br />Next: ". $check['Queue.DateTime']->sec;
//die;

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

	public function remove_order($params) {


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

	public function release_lock() {

		$this->live = false;

		$foo = Parameters::find('all');
		$foo->save(array('Queue' => array('Locked' => 0, 'DateTime' => '')));

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
