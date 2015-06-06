<?php
namespace app\controllers;

use app\models\Orders;
use app\models\Trades;
use app\extensions\action\Money;

class APIController extends \app\extensions\action\Controller {

	public function index() {

return 'BTC API';
//	return;
	}

	public function orders($market, $limit = false) {


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

		//put in correct order
		array_multisort($result['bids'], SORT_DESC,
				$result['asks'], SORT_ASC);

		if($limit) {

		$result['bids'] = array_slice($result['bids'], 0, $limit);
		$result['asks'] = array_slice($result['asks'], 0, $limit);
		}

		return $this->render(array('json' => $result, 'status'=> 200));
	}

	public function transactions($market, $time_limit = false, $min_amount = 1) {

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


}
?>
