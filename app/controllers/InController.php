<?php
namespace app\controllers;

//use app\extensions\action\Security;
//use app\extensions\action\OAuth2;
//use app\models\Users;
//use app\models\Details;
use app\models\Addresses;
use app\models\Transactions;
use app\models\Orders;
use app\models\Parameters;
use app\models\Settings;
use app\models\File;
use lithium\data\Connections;
use app\extensions\action\Functions;
use app\extensions\action\Coinprism;
use app\extensions\action\Money;
//use app\extensions\action\Bitcoin;
//use app\extensions\action\Litecoin;
//use app\extensions\action\Greencoin;
//use lithium\security\Auth;
//use lithium\storage\Session;
use app\extensions\action\GoogleAuthenticator;
use lithium\util\String;
use MongoID;
use \lithium\template\View;
use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;

class InController extends \app\extensions\action\Controller {

	public function index() {
	}

	public function accounts() {

		$this->security();
		$user_id = $this->get_user_id();

         	$title = 'Account Balances';

		$money = new Money($user_id);

		global $currencies;
		
		foreach($currencies as $currency) {

			$data['Available Balance'][$currency] = $money->get_balance($currency, true);
			$data['Pending Deposits'][$currency] = $money->pending_deposits($currency, true);
			$data['Pending Withdrawals'][$currency] = $money->pending_withdrawals($currency, true);
			$data['Open Buy Orders'][$currency] = $money->pending_buy_orders($currency, true);
			$data['Open Sell Orders'][$currency] = $money->pending_sell_orders($currency, true);	
 		}

                return compact('data');

	}

        public function orders() {

		$this->security();
		$user_id = $this->get_user_id();
                
		$title = 'Open Orders';

		$first_curr = 'btc';
		$second_curr = 'dct';

		$YourOrders = Orders::find('all',array(
			'conditions'=>array(
				'user_id'=>$user_id,
				'Completed'=>'N',
				),
			'order' => array('DateTime'=>-1)
		));

                return compact('YourOrders');

        }


	public function transactions() {

		$this->security();
		$user_id = $this->get_user_id();
		$details = $this->get_details();

         	$title = 'Transactions';

		$transactions = Transactions::find('all',array(
                        'conditions'=>array(
			'user_id' => $user_id,
                        ),
                        'order'=>array('DateTime'=>-1)
                ));

	$money = new Money($user_id);

	foreach($transactions as $tx) {


	$amount = $money->display_money($tx['Amount'], $tx['Currency']);

	$trans['ALL'][] = array('_id' => $tx['_id'], 'DateTime' => $tx['DateTime'], 'Currency' => $tx['Currency'], 'Type' => $tx['TransactionType'], 'Amount' => $amount, 'Status' => $tx['Status'], 'Hash' => $tx['TransactionHash']);
	$trans[$tx['Currency']][] = array('_id' => $tx['_id'], 'DateTime' => $tx['DateTime'], 'Currency' => $tx['Currency'], 'Type' => $tx['TransactionType'], 'Amount' => $amount, 'Status' => $tx['Status'], 'Hash' => $tx['TransactionHash']);
	}
	$transactions = $trans;

                return compact('title','details','transactions');

	}


	public function withdraw($currency='btc') {

		$this->security();
		$user_id = $this->get_user_id();
		
		$currency = strtoupper($currency);

	        $title = 'Withdraw Funds';

		$money = new Money($user_id);
		$balances = $money->get_balances();

 		$paytxfee = Parameters::find('first');
                $txfee = $paytxfee['paytxfee'];
                
		$transactions = Transactions::find('all',array(
                                'conditions'=>array(
	 			                   '$or' => array(
                                                                array('Status' => 'emailpending'),
                                                                array('Status' => 'processing')
                                                                ),
                                                'user_id' =>  $user_id,
                                                'Currency' => $currency,
                                                'TransactionType' => 'Withdrawal')
              					));


        foreach($transactions as $tx) {

	$amount = $tx['Amount'] * -1;
        $amount = $money->display_money($amount, $tx['Currency']); //NOTE: show withdrawals as a positive amount

        $trans['ALL'][] = array('_id' => $tx['_id'], 'DateTime' => $tx['DateTime'], 'Currency' => $tx['Currency'], 'Type' => $tx['TransactionType'], 'Amount' => $amount, 'Status' => $tx['Status'], 'Hash' => $tx['TransactionHash']);
        
	$trans[$tx['Currency']][] = array('_id' => $tx['_id'], 'DateTime' => $tx['DateTime'], 'Address' => $tx['Address'], 'Currency' => $tx['Currency'], 'Type' => $tx['TransactionType'], 'Amount' => $amount, 'Status' => $tx['Status'], 'Hash' => $tx['TransactionHash']);
        }
        $transactions = $trans;

                        return compact('title', 'balances', 'transactions','user','currency');
			

                return;
	}

    public function deposit($currency='btc'){

		$this->security();
		$user_id = $this->get_user_id();
		$details = $this->get_details();                

		$currency = strtoupper($currency);

                $title = 'Deposit Funds';
                
		$secret = $details['secret'];


		/*
			New cc implementation
		*/

			//generate a new address
			if($this->request->data){ 

		 	$coinprism = new Coinprism( COINPRISM_USERNAME, COINPRISM_PASSWORD );
			$new_addresses = $coinprism->create_address($user_id);

			$this->makedefault($new_addresses['btc_address']);
			
			}

			

		//
		//Can't find a way to pick the default out of the full results, so doing two data retrievals.
		//

		$default_addresses = Addresses::find('first', array(
				'conditions' => array('user_id' => $user_id,
						      'default' => '1')
			));

			//first time here?
			if(count($default_addresses) == 0) {

		 	$coinprism = new Coinprism( COINPRISM_USERNAME, COINPRISM_PASSWORD );
			$new_addresses = $coinprism->create_address($user_id);

			$this->makedefault($new_addresses['btc_address']); //will reload page
			}
	
		$addresses = Addresses::find('all', array(
				'conditions' => array('user_id' => $user_id,
						      'default' => array('!=' => '1'))
			));

                        
		return compact('details','default_addresses', 'addresses', 'title', 'foo');

        }


	public function makedefault($btc_address) {

		$this->security();
		$user_id = $this->get_user_id();
		$details = $this->get_details();                
		
		$new = Addresses::find('first', array(
			'conditions' => array('user_id' => $user_id,
					      'btc_address' => $btc_address)
					));
		
		if(0==count($new)) return $this->redirect('in/deposit'); //invalid address, stop here

		$old = Addresses::find('all', array(
                          'conditions' => array('user_id' => $user_id,
                                                'default' => '1')
                        		));

		$old->save(array('default' => ''));	
		$new->save(array('default' => '1'));

		return $this->redirect('/in/deposit'); //done
	}


	public function removetransaction($TransactionID,$ID,$url,$currency){

		$this->security();              

		 $tx = Transactions::find('first', array(
                        'conditions' => array('_id' => new MongoID($ID))
                ));
                        if(String::hash($tx['_id'])==$TransactionID){
  
			$data = array('Status' => 'cancelled');

			$tx->save($data);

			$amount = $tx['Amount'] * -1;

			$money = new Money($tx['user_id']);
			$money->update_balance($amount, $tx['Currency']);                         

                        }

                return $this->redirect('/in/'.$url.'/'.$currency);
        }


	public function paymentverify($currency=null){

		$this->security();
		$user_id = $this->get_user_id();
		$details = $this->get_details();
		$email  =  $this->get_email();

		if($currency==""){
				return compact('details');
		}

		$currency = strtoupper($currency);
		
	
		if ($this->request->data) {
			
			$money = new Money($user_id);
			
			$amount = $money->undisplay_money($this->request->data['amount'], $currency);
			if($details['balance.'.$currency]<=$amount){return false;}			
			$amount = $amount * -1;
			
			$address = $this->request->data['currencyaddress'];
			
			$tx = Transactions::create();
				$data = array(
					'DateTime' => new \MongoDate(),
					'user_id' => $user_id,
					'TransactionType' => 'Withdrawal',
					'Address'=>$address,							
					'verify.payment' => sha1(openssl_random_pseudo_bytes(4,$cstrong)),
					'Paid' => 'No',
					'Amount'=> (int) $amount,
					'Currency'=> $currency,					
					'Added'=>false,
					'Status' => 'emailpending'
				);							
				$tx->save($data);	
			
			$money->update_balance($amount, $currency);
			$data['Amount'] = $money->display_money($amount, $currency);
	
			$view  = new View(array(
				'loader' => 'File',
				'renderer' => 'File',
				'paths' => array(
					'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
				)
			));
			$body = $view->render(
				'template',
				compact('data','details','tx','currency'),
				array(
					'controller' => 'in',
					'template'=>'withdrawDigital',
					'type' => 'mail',
					'layout' => false
				)
			);
			$transport = Swift_MailTransport::newInstance();
			$mailer = Swift_Mailer::newInstance($transport);
	
			$message = Swift_Message::newInstance();
			$message->setSubject($currency." Withdrawal Approval from ".COMPANY_URL);
			$message->setFrom(array(NOREPLY => $currency.' Withdrawal Approval email '.COMPANY_URL));
			$message->setTo($email);
			$message->addBcc(MAIL_1);
			$message->addBcc(MAIL_2);			
			$message->addBcc(MAIL_3);		
			$message->setBody($body,'text/html');
			
			$mailer->send($message);
				
		}	
		return compact('data', 'details', 'currency');
	}


	public function paymentconfirm($currency=null,$id = null){

		if ($id==""){return $this->redirect('/login');}

		$transaction = Transactions::find('first',array(
			'conditions'=>array(
				'verify.payment'=>$id,
				'Currency'=>$currency,
				'TransactionType' => 'Withdrawal',
				'Status'=>'emailpending'
				)
		));

		$money = new Money($transaction['user_id']);
		$amount = $transaction['Amount'] * -1;
		$transaction['Amount'] = $money->display_money($amount, $transaction['Currency']);


		$details = Details::find('first', array(
					'conditions' => array(
					'user_id' => $transaction['user_id'],
					)
					));
		$username = $details['username'];

		return compact('transaction','username','currency');
	}

	public function paymentadmin(){

		if ($this->request->data) {

			$verify = $this->request->data['verify'];
			$user_id = $this->request->data['user_id'];
			$username = $this->request->data['username'];
			$password = $this->request->data['password'];
			$currency = $this->request->data['currency'];
			if($password==""){
				return $this->redirect(array('controller'=>'in','action'=>'paymentconfirm/'.$currency.'/'.$verify));
			}
			$transaction = Transactions::find('first',array(
				'conditions'=>array(
					'verify.payment'=>$verify,
					'user_id'=>$user_id,
					'Currency'=>$currency,
					'Status'=>'emailpending'
					)
			));
			$user = Users::find('first',array(
				'conditions' => array(
					'username' => $username,
					'password' => String::hash($password),
				)
			));
			$user_id = $user['_id'];
		
			if($user_id==""){
				return $this->redirect(array('controller'=>'in','action'=>'paymentconfirm/'.$currency.'/'.$verify));
			}
		
			$data = array('Status' => 'processing');	
			$transaction->save($data);

			return;	
		}
	}

	        public function twofactor() {

			$user = Session::read('default');
	                if ($user==""){         return $this->redirect('/login');}
        	        $user_id = $user['_id'];

			if($user['OTPVerified']) $this->redirect('In::accounts'); //already done 2fa

				if(isset($this->request->data['2FA'])) {

				$details = Details::find('first', array(
                                	        'conditions'=>array(
                                        	        'user_id'=> $user_id)
                                                	));

					$ga = new GoogleAuthenticator();

                                    	if(! $ga->verifyCode($details['secret'], $this->request->data['2FA'], 2)) {

                                    	$error = 'Invalid Two Factor Code';
				
					return compact('error');
					}

					$user['OTPVerified'] = true;

			//must be good
			Session::write('default', $user);
			return $this->redirect('In::accounts');
			}		
	
        	        return;

        	}


}

?>
