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

		$this->secure('api', $details);

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

                return $this->redirect('/in/'.$url.'/'.$currency);
        }


	public function paymentverify($currency=null){

		$this->secure();
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
			if($details['balance.'.$currency] < $amount) {

			$balance = $details['balance.'.$currency];	

			$error = 'Insufficient funds';
 			return compact('details', 'error', 'amount', 'currency', 'balance');
			}			
			
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


       public function forgotpassword(){
        
        if($this->request->data){
                        $msg = "If the username exists then we have sent password reset instructions to their registered email address.";
        
		$username = $this->request->data['username'];

		$details = Details::find('first', array(
				'conditions' => array(
					'username' => $username,
					)));

		//user found
		if(1 == count($details)) {

			$email = Emails::find('first', array(
				'conditions' => array(
					'user_id' => $details['user_id'],
					'Default' => true,
					)
					));

			$email = $email['Email'];
                	$key = $details['key'];


                if($key!=""){
              
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
                        $message->addBcc(MAIL_1);
                        $message->addBcc(MAIL_2);
                        $message->addBcc(MAIL_3);

                        $message->setBody($body,'text/html');
                        $mailer->send($message);
                        }
                
		} //user found

                return compact('msg');
       	    } //post data
	 }


	public function changepassword($key = false) {


		if(! $key){ return $this->redirect('/login');}
		
		$details = Details::find('first', array(
				'conditions' => array(
					'key' => $key,
					)
					));

		//invalid key
		if(0 == count($details)) return $this->redirect('/login');

		$user_id = $details['user_id'];
	
		if('1' == $details['TOTP.Validate']) $TwoFactorEnabled = true;
		else $TwoFactorEnabled = false;


		//form submitted		
		if($this->request->data){
		
		$email = $this->request->data['email'];
		
		$ga = new GoogleAuthenticator();

			if( ('' == $this->request->data['password']) OR ('' == $email) ) {

			$error = "All fields are required";
			}
			elseif($this->request->data['password'] != $this->request->data['password2']) {

			$error = "Password fields do not match!";
			}
			elseif( ($TwoFactorEnabled) && (! $ga->verifyCode($details['secret'], $this->request->data['2FA'], 2)) ) {

			$error = "Invalid Two Factor Code!";
			}
			else {

				$check = Emails::find('first', array(
						'conditions' => array(
							'Email' => $email,
							'user_id' => $user_id,
							'Default' => true,
							)
							));

				if(0 == count($check)) {

				$error = "Password not changed"; //don't tell them why
				}
			}
			
			if(! $error) {

			$user = Users::find('first', array(
				'conditions' => array(
					'user_id' => $user_id,
					)
					));

			$data = array(
                                     'password' => String::hash($this->request->data['password']),
                                     );

			$user->save($data);

			return $this->redirect('/login/1/'); //redirect with success message
			}		

		}
		return compact('TwoFactorEnabled', 'key', 'error');
	}
		public function password(){
		if($this->request->data){
			$details = Details::find('first', array(
				'conditions' => array(
					'key' => $this->request->data['key'],
				),
				'fields' => array('user_id')
			));
			$msg = "Password Not Changed!";
//			print_r($details['user_id']);
			if($details['user_id']!=""){
				if($this->request->data['password'] == $this->request->data['password2']){
//					print_r($this->request->data['password']);
					
					$user = Users::find('first', array(
						'conditions' => array(
							'_id' => $details['user_id'],
						)
					));
//					print_r($user['password']);
						if($user['password']!=String::hash($this->request->data['password'])){
							print_r($details['user_id']);
							
							$data = array(
							'password' => String::hash($this->request->data['password']),
							);
//							print_r($data);
							
							$user = Users::find('all', array(
								'conditions' => array(
								'_id' => $details['user_id'],
								)
							))->save($data,array('validate' => false));
					//		print_r($user);
						
							if($user){
								$msg = "Password changed!";
							}
						}else{
								$msg = "Password same as old password!";
						}
					}else{
						$msg = "New password does not match!";
					}
			}
		}
		return compact('msg');
	

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
