<?php

namespace app\extensions\action;

use app\models\Details;
use app\models\Transactions;

/*
	We can use this class without a user_id if we just want to use the display / undisplay_money methods.
*/

class Money extends \lithium\action\Controller{

private $user_id;
private $balances; //array
private $open_balances; //array
private $btc;
private $tcp;
private $dct;
private $btc_unit;


	public function __construct($user_id = false) {

	if($user_id) {

		$details = Details::find('first',
			array('conditions'=>array('user_id'=>$user_id))
		);

		$this->user_id = $user_id;

		//balances	
       		$this->balances['BTC'] = $details['balance.BTC'];
       		$this->balances['TCP'] = $details['balance.TCP'];
        	$this->balances['DCT'] = $details['balance.DCT'];
		
		$this->open_balances['BTC'] = $details['OpenBalance.BTC'];
		$this->open_balances['TCP'] = $details['OpenBalance.TCP'];
		$this->open_balances['DCT'] = $details['OpenBalance.DCT'];

		}

		//currency symbols
       		$this->btc = 'BTC';
        	$this->tcp = 'TCP';
        	$this->dct = 'DCT';
        
		$this->btc_unit = 'BTC';
	}



	public function update_balance($amount, $currency) {

		$currency = strtoupper($currency);

		$details = Details::find('first',
			array('conditions'=>array('user_id'=>$this->user_id))
		);
		$this->balances[$currency] = $this->balances[$currency] + $amount;
	
		$details->save(array("balance.$currency" => $this->balances[$currency]));

		return true;
	}

	public function get_balances() {

		$balances = $this->balances;

		array_walk($balances, array($this, 'display_money'));
	
		return $balances;
	}

	public function get_balance($currency, $forprint = false) {

		$currency = strtoupper($currency);
	
		$balance = $this->balances[$currency];

		if($forprint) return $this->display_money($balance, $currency);

		else
		 return $balance;
	}

	public function display_money(&$amount, $currency) {

		$currency = strtoupper($currency);

		if('BTC' == $currency) $amount = number_format($amount / 100000000, 8);

		elseif( ('TCP' == $currency) OR ('DCT' == $currency) ) $amount = number_format($amount / 100, 2);

		else $amount = 'ERROR!';

		return $amount;
	}


	public function undisplay_money($amount, $currency) {

        	$currency = strtoupper($currency);

        	if('BTC' == $currency) $amount = $amount * 100000000;

        	elseif( ('TCP' == $currency) OR ('DCT' == $currency) ) $amount = $amount * 100; 

      		else $amount = false;

        	return intval(strval($amount));
        }

	public function pending_deposits($currency, $forprint = false) {

        	$currency = strtoupper($currency);

		$foo = Transactions::find('all',
			array('conditions'=>array('user_id'=>$this->user_id,
						  'Currency' => $currency,
						  'TransactionType' => 'Deposit',
						  'Status' => array('!=' => 'completed'))
		));
		
		$total = 0;

		foreach($foo as $foo) { $total = $total + $foo['Amount']; }

		if($forprint) $total = $this->display_money($total, $currency);

		return $total;
	}
	
	public function pending_withdrawals($currency, $forprint = false) {

        	$currency = strtoupper($currency);

		$foo = Transactions::find('all',
			array('conditions'=>array(
						'$or' => array(
								array('Status' => 'emailpending'),
								array('Status' => 'processing')
								),
						'user_id' =>  $this->user_id,
					        'Currency' => $currency,
					        'TransactionType' => 'Withdrawal')
					   ));
		
		$total = 0;

		foreach($foo as $foo) { $total = $total - $foo['Amount']; }

		if($forprint) $total = $this->display_money($total, $currency);

		return $total;
	}

	public function open_balance($currency, $forprint = false) {

	$currency = strtoupper($currency);

	if($forprint) return $this->display_money($this->open_balances[$currency], $currency);

	else return $this->open_balances[$currency];
	
	}
	
	public function pending_buy_orders($currency, $forprint = false) {

	return 0;
	}

	public function pending_sell_orders($currency, $forprint = false) {

	return 0;
	}
}

?>
