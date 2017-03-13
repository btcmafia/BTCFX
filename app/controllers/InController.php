<?php
namespace app\controllers;

use app\models\Addresses;
use app\models\Transactions;
use app\models\Orders;
use app\models\Parameters;
use app\models\Settings;
use app\models\Emails;
use app\models\Details;
use app\models\File;
use lithium\data\Connections;
use lithium\storage\Session;
use app\extensions\action\Functions;
use app\extensions\action\Coinprism;
use app\extensions\action\Money;
use \CoinAddress;
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

	public function test() {

		//$coinprism = new Coinprism( COINPRISM_USERNAME, COINPRISM_PASSWORD );
                //$foo = $coinprism->create_address('55741216487e78c10f8b456c', 'DUST CUSTOM ADDRESS');

		$array1 = array('john', 'david', 'ben');

		$array2 = array('sophie', 'silvie', 'charlotte');

		//$foo = array_unique(array_merge($array1, $array2));

	return compact('foo');	
	}

	public function accounts() {

		$this->secure();
		$user_id = $this->get_user_id();

         	$title = 'Account Balances';

		$money = new Money($user_id);

		global $currencies;
		
		foreach($currencies as $currency) {

			$data['Available Balance'][$currency] = $money->get_balance($currency, true);
			$data['Pending Deposits'][$currency] = $money->pending_deposits($currency, true);
			$data['Pending Withdrawals'][$currency] = $money->pending_withdrawals($currency, true);
			$data['Open Orders'][$currency] = $money->open_balance($currency, true);
 		}

                return compact('data');

	}	

        public function orders($api = false, $details = false) {

		$this->secure($api, $details);

		$user_id = $this->get_user_id();
                
		$title = 'Open Orders';

		$first_curr = 'btc';
		$second_curr = 'tcp';

		$YourOrders = Orders::find('all',array(
			'conditions'=>array(
				'user_id'=>$user_id,
//				'Completed'=>'N',
				),
			'order' => array('DateTime'=>-1)
		));

		$money = new Money();

		if($api) {

			foreach($YourOrders as $foo) {

//format the date from now on
$foo['DateTime'] = gmdate('d-M-Y H:i:s',$foo['DateTime']->sec);

//format money
$foo['Amount'] = $money->display_money($foo['Amount'], $foo['FirstCurrency']);
$foo['Price'] = $money->display_money($foo['Price'], $foo['SecondCurrency']);


        if( ('BTC' == $foo['FirstCurrency']) && ('TCP' == $foo['SecondCurrency']) ) {

                $btc_tcp[] = array('id' => (string) $foo['_id'], 'datetime' => $foo['DateTime'], 'type' => $foo['Type'], 'amount' => $foo['Amount'], 'price' => $foo['Price'], 'expires' => $foo['Expires']);
        }

        elseif( ('BTC' == $foo['FirstCurrency']) && ('DCT' == $foo['SecondCurrency']) ) {

                $btc_dct[] = array('id' => (string) $foo['_id'], 'datetime' => $foo['DateTime'], 'type' => $foo['Type'], 'amount' => $foo['Amount'], 'price' => $foo['Price'], 'expires' => $foo['Expires']);
        }
        if( ('TCP' == $foo['FirstCurrency']) && ('DCT' == $foo['SecondCurrency']) ) {

                $tcp_dct[] = array('id' => (string) $foo['_id'], 'datetime' => $foo['DateTime'], 'type' => $foo['Type'], 'amount' => $foo['Amount'], 'price' => $foo['Price'], 'expires' => $foo['Expires']);
        }

		} //foreach

		$orders = array('btc_tcp' => $btc_tcp, 'btc_dct' => $btc_dct, 'tcp_dct' => $tcp_dct);

		return $orders;
		} //api

//		array_walk($YourOrders, array($this, 'prepare_money_display'), $money);

                return compact('YourOrders', 'user_id');

        }

	public function prepare_money_display(&$array, $money) {

//	var_dump($money);
//die;
	//$array['Amount'] = $money->display_money($array['Amount'], $array['FirstCurrency']);
	//$array['Price'] = $money->display_money($array['Price'], $array['SecondCurrency']);

	return;
	}

	//TODO: need to limit the number of results
	public function transactions($api = false, $details = false) {

		$this->secure($api, $details);

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

	$count = 0;
	
	foreach($transactions as $tx) {


	$amount = $money->display_money($tx['Amount'], $tx['Currency']);


	//formatted differently for the api
	if($api) {

$tx['DateTime'] = gmdate('d-M-Y H:i:s',$tx['DateTime']->sec); 

	//$trans['ALL'][$count] = array('id' => (string) $tx['_id'], 'datetime' => $tx['DateTime'], 'currency' => $tx['Currency'], 'type' => $tx['TransactionType'], 'amount' => $amount, 'status' => $tx['Status']);
	$trans[$tx['Currency']][$count] = array('id' => (string) $tx['_id'], 'datetime' => $tx['DateTime'], 'currency' => $tx['Currency'], 'type' => $tx['TransactionType'], 'amount' => $amount, 'status' => $tx['Status']);
	
	//only have the tx_hash if it exists, i.e not for trades
	if(isset($tx['TransactionHash'])) { 
						$trans['ALL'][$count]['tx_hash'] = $tx['TransactionHash']; 
						$trans[$tx['Currency']][$count]['tx_hash'] = $tx['TransactionHash'];
					 }

	$count++;
	}

	else { //not api

	$trans['ALL'][] = array('_id' => $tx['_id'], 'DateTime' => $tx['DateTime'], 'Currency' => $tx['Currency'], 'Type' => $tx['TransactionType'], 'Amount' => $amount, 'Status' => $tx['Status'], 'Hash' => $tx['TransactionHash']);

	$trans[$tx['Currency']][] = array('_id' => $tx['_id'], 'DateTime' => $tx['DateTime'], 'Currency' => $tx['Currency'], 'Type' => $tx['TransactionType'], 'Amount' => $amount, 'Status' => $tx['Status'], 'Hash' => $tx['TransactionHash']);
	

	}

	} //foreach

	$transactions = $trans;

	if($api) return $transactions;

                return compact('title','details','transactions');

	}


	public function withdraw($currency='btc') {

		$this->secure();
		$user_id = $this->get_user_id();
	
		$currency = strtoupper($currency);

	        $title = 'Withdraw Funds';

		$money = new Money($user_id);
		$balances = $money->get_balances();

 		$paytxfee = Parameters::find('first');
                $txfee = $paytxfee['paytxfee'];

	
		if ($this->request->data) {
		
			if(! $this->validate_currency($this->request->data['currency']) ) $error = 'Invalid currency';
			else $currency = $this->request->data['currency']; 
	
			$money = new Money($user_id);
		
			$amount = $this->request->data["Amount$currency"];
	
			if($balances[$currency] < $amount) $error = 'Insufficient funds';
			
			$amount = $money->undisplay_money($amount, $currency);

			if($amount <= 0) $error = 'Invalid amount';

			$amount = $amount * -1;
			
			$address = $this->request->data["CurrencyAddress$currency"];

			if(! Coinprism::validate_address($address, $currency) ) $error = 'Invalid address';


		if(! isset($error)) {
		/*
			This all needs to be queued!
		*/

		$email = $this->get_email();	
		$details = $this->get_details();
	
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
				compact('data','details', 'address', 'currency'),
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
			$message->setBody($body,'text/html');
			
			$mailer->send($message);
				
		return $this->redirect("/in/paymentrequested/$currency/");		
		}
		}

                
		$transactions = Transactions::find('all',array(
                                'conditions'=>array(
	 			                   '$or' => array(
                                                                array('Status' => 'emailpending'),
                                                                array('Status' => 'processing')
                                                                ),
                                                'user_id' =>  $user_id,
                                                //'Currency' => $currency,
                                                'TransactionType' => 'Withdrawal')
              					));


        foreach($transactions as $tx) {

	$amount = $tx['Amount'] * -1;
        $amount = $money->display_money($amount, $tx['Currency']); //NOTE: show withdrawals as a positive amount

        $trans['ALL'][] = array('_id' => $tx['_id'], 'DateTime' => $tx['DateTime'], 'Currency' => $tx['Currency'], 'Type' => $tx['TransactionType'], 'Amount' => $amount, 'Status' => $tx['Status'], 'Hash' => $tx['TransactionHash']);
        
	$trans[$tx['Currency']][] = array('_id' => $tx['_id'], 'DateTime' => $tx['DateTime'], 'Address' => $tx['Address'], 'Currency' => $tx['Currency'], 'Type' => $tx['TransactionType'], 'Amount' => $amount, 'Status' => $tx['Status'], 'Hash' => $tx['TransactionHash']);
        }
        
	$transactions = $trans;

                        return compact('title', 'balances', 'transactions','user','currency', 'error');
	
	}

    public function deposit($currency='btc'){

		$this->secure();
		$user_id = $this->get_user_id();
		$details = $this->get_details();                

		$currency = strtoupper($currency);

                $title = 'Deposit Funds';
                
		$secret = $details['secret'];


			//generate a new address
			if($this->request->data){ 

		 	$coinprism = new Coinprism( COINPRISM_USERNAME, COINPRISM_PASSWORD );
			$new_addresses = $coinprism->create_address($user_id);

			if(! isset($new_addresses['error']) ) $this->makedefault($new_addresses['btc_address']); 
			else $error = $new_addresses['error'];
			}

			

		$default_addresses = Addresses::find('first', array(
				'conditions' => array('user_id' => $user_id,
						      'default' => '1')
			));

			//first time here?
			if(count($default_addresses) == 0) {

		 	$coinprism = new Coinprism( COINPRISM_USERNAME, COINPRISM_PASSWORD );
			$new_addresses = $coinprism->create_address($user_id);

			if(! isset($new_addresses['error']) ) $this->makedefault($new_addresses['btc_address']); //will reload page
			else $error = $new_addresses['error'];
			}
	
		$addresses = Addresses::find('all', array(
				'conditions' => array('user_id' => $user_id,
						      'default' => array('!=' => '1'))
			));

                        
		return compact('details','default_addresses', 'addresses', 'title', 'foo', 'error');

        }


	public function makedefault($btc_address) {

		$this->secure();
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

		$this->secure();              

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

                return $this->redirect('/in/'.$url.'/');
        }


	public function paymentrequested($currency=null){

		$this->secure();

		$user_id = $this->get_user_id();
		$details = $this->get_details();
		$email  =  $this->get_email();

		if($currency==""){

			return $this->redirect('/in/withdraw/');
		}

		$currency = strtoupper($currency);
		
		return compact('details', 'currency');
	}


	public function paymentconfirm($currency=null, $verify_code = null){

		$this->secure();

		$user_id = $this->get_user_id();
		$details = $this->get_details();
		$username = $details['username'];
		

		if ($this->request->data) {

			$verify_code = $this->request->data['verify'];
			$password = $this->request->data['password'];
			$currency = $this->request->data['currency'];
			

			$transaction = Transactions::find('first',array(
				'conditions'=>array(
					'verify.payment'=>$verify_code,
					'user_id'=>$user_id,
					'Currency'=>$currency,
					'Status'=>'emailpending'
					)
			));

			//check passwd
                        if( ($password == '') OR (! $this->validate_password($user_id, $password) )) {
	
			$error = 'Invalid password';
			}		

			//check 2fa
			if(1 == $details["TOTP.Validate"]) {
 				
				$code = $this->request->data['code']; //2fa
		
				$ga = new GoogleAuthenticator();

                                if(! $ga->verifyCode($details['secret'], $code, 1)) $error = 'Invalid Two Factor Authentication code'; 

			}

			if($password=="") $error = 'Password is required';

			if(! isset($error) ) {
			
				$data = array('Status' => 'processing');	
				$transaction->save($data);

				return $this->redirect('/in/paymentprocessed/');
			}
		}


		$transaction = Transactions::find('first',array(
			'conditions'=>array(
				'user_id' => $user_id,
				'verify.payment'=>$verify_code,
				'Currency'=>$currency,
				'TransactionType' => 'Withdrawal',
				'Status'=>'emailpending'
				)
		));

		if(0 == count($transaction)) return; 

		if ($verify_code==""){return $this->redirect('/login');}

	        if(1 == $details["TOTP.Validate"]) $TwoFactorEnabled = true;
                else    $TwoFactorEnabled = false;

		$money = new Money($transaction['user_id']);
		$amount = $transaction['Amount'] * -1;
		$transaction['Amount'] = $money->display_money($amount, $transaction['Currency']);


		return compact('transaction', 'currency', 'TwoFactorEnabled', 'error');
	}

	public function paymentprocessed(){

		$this->secure();

	}


       public function forgotpassword(){
        
        if($this->request->data){
                
		//don't confirm whether username exists
		$msg = "If the username exists then we have sent password reset instructions to their registered email address.";
        
		$username = $this->request->data['username'];

		$details = Details::find('first', array(
				'conditions' => array(
					'username' => $username,
					)));

		//user found
		if(1 == count($details)) {

		//generate a new reset key and expiry, and save them
		$ga = new  GoogleAuthenticator();
		$key = $ga->createSecret(64);
		$expiry = time() + 60 * 15; //15 min
		
		$details->save(array('PasswordReset.Key' => $key, 'PasswordReset.Expiry' => $expiry));

			$email = Emails::find('first', array(
				'conditions' => array(
					'user_id' => $details['user_id'],
					'Default' => true,
					)
					));

			$email = $email['Email'];

		             
 
	          $view  = new View(array(
                                'loader' => 'File',
                                'renderer' => 'File',
                                'paths' => array(
                                        'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
                                )
                        ));
                        $body = $view->render(
                                'template',
                                compact('username','email','key'),
                                array(
                                        'controller' => 'in',
                                        'template'=>'forgotpassword',
                                        'type' => 'mail',
                                        'layout' => false
                                )
                        );

                        $transport = Swift_MailTransport::newInstance();
                        $mailer = Swift_Mailer::newInstance($transport);

                        $message = Swift_Message::newInstance();
                        $message->setSubject("Password reset link from ".COMPANY_URL);
                        $message->setFrom(array(NOREPLY => 'Password reset email '.COMPANY_URL));
                        $message->setTo($email);

                        $message->setBody($body,'text/html');
                        $mailer->send($message);
                        
                
		} //user found

       	    } //post data
                return compact('msg');
	 }


	public function changepassword($key = '') {

		if('' == $key) $this->redirect('/login');

		$details = Details::find('first', array(
				'conditions' => array(
					'PasswordReset.Key' => $key,
					)
					));	

		if(0 == count($details)) return $this->redirect('/login');

		//has it expired already?
		if(time() > $details['PasswordReset.Expiry']) return $this->redirect('/in/expiredkey');

               	if(1 == $details["TOTP.Validate"]) $TwoFactorEnabled = true;
               	else    $TwoFactorEnabled = false;


			if($this->request->data) {

			//we can pretend it's an api call to get easy access to their email address without them being logged in and without creating a session
			$this->secure('api', $details);

			$user_id = $this->get_user_id();

		//	$email = $this->request->data['email'];	
			$password = $this->request->data['password'];	
			$password2 = $this->request->data['password2'];	

				if($password != $password2) {

					 $error = 'Password fields do not match';			
				}
			
			/*	
				elseif($this->get_email() != $email) {

					$error = 'Invalid email address';
				}	
			*/
				elseif($TwoFactorEnabled) {

                			$ga = new GoogleAuthenticator();
                                
					if(! $ga->verifyCode($details['secret'], $this->request->data['code'], 1)) $error = 'Invalid Two Factor Authentication code'; 
				}

			if(! isset($error) ) {

				$this->update_password($user_id, $password);

				//$message = 'Your password has been updated. You may now <a href="/login/">login</a>.';
		
				//delete the key
				$details->save(array('PasswordReset.Key' => '', 'PasswordReset.Expiry' => ''));

				//success message is shown on the login page			
				return $this->redirect('/login/1/');
				exit;
			}
	
			} //POST

		return compact('message', 'error', 'key', 'TwoFactorEnabled');
	}

	public function expiredkey() {


	return;
	}

		public function splash() {

		        $this->secure();
        	        $details = $this->get_details();
                	$ga = new GoogleAuthenticator();

               		if(1 == $details["TOTP.Validate"]) $TwoFactorEnabled = true;
                	else    $TwoFactorEnabled = false;

			$key = $details['key'];

                        if(! $TwoFactorEnabled) {

                        $qrcode = $ga->getQRCodeGoogleUrl(COMPANY_URL, $details['secret']);
                        }


		return compact('TwoFactorEnabled', 'qrcode', 'key');
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
