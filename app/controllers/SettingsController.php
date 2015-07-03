<?php

namespace app\controllers;

use lithium\storage\Session;
use app\models\Details;
use app\models\Users;
use app\models\Emails;
use app\extensions\action\ActionLog;
use app\extensions\action\Coinprism;
use app\extensions\action\GoogleAuthenticator;

use \lithium\template\View;
use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;

class SettingsController extends \app\extensions\action\Controller {

        public function index() {
      
	return;
	}

	public function profile() {

		$this->secure();
		
		$user_id = $this->get_user_id();
		$details = $this->get_details();

		$emails = Emails::find('first', array(
					'conditions' => array(
						'user_id' => $user_id,
						'Default' => true,
						'Verified' => true)
						));

		$email = $emails['Email']; //current	
		$current_email = $email; //we'll switch back to this on successful update, because it won't be validated yet


		if(1 == $details["TOTP.Validate"]) $TwoFactorEnabled = true;
		else	$TwoFactorEnabled = false;
	
		//see if they are in the process of validating another email
		$UnvalidatedEmail = Emails::find('first', array(
					'conditions' => array(
						'user_id' => $user_id,
						'Verified' => false)
						));	


			if($this->request->data['submit-email']) {
			//TODO: validate the email, perhaps in models/Emails?
			
			if(1 == count($UnvalidatedEmail)) return compact('emails', 'TwoFactorEnabled', 'UnvalidatedEmail');
			
			$email = $this->request->data['Email'];

			if($email == $current_email) {

			$error = "$email is already your email address";
			return compact('emails', 'details', 'error', 'TwoFactorEnabled');
			}
				  
					//check 2fa
                                        if($TwoFactorEnabled) {

                                                $ga = new GoogleAuthenticator();

                                                if(! $ga->verifyCode($details['secret'], $this->request->data['2FA'], 2)) {

                                                $error = 'Invalid Two Factor Code';
                                                return compact('emails', 'details', 'error', 'TwoFactorEnabled');
                                                }

                                        } //end 2fa
	
						//check password
						if(! $this->validate_password($user_id, $password) ) {

						$error = 'Password is incorrect';
                                                return compact('emails', 'error', 'TwoFactorEnabled');
						}


					//check doesn't exist
					$search = Emails::find('first', array(
							'conditions' => array(
								'Email' => $email,
								'Verified' => true)
								));
					if(0 != count($search)) {
					
						//is it an old address of the same user, validated but not default?
						if($user_id == $search['user_id']) {

						//just make it default, and the old one not, and be done!
						$search->save(array('Default' => true));
						$emails->save(array('Default' => false));
						$users->save(array('email' => $email), array('validate' => false));
						$emails['Email'] = $email;

					//log the action
					$log = new ActionLog();
					$log->update_email($user_id, $email);

						$message = 'Your email address has been updated';
						return compact('emails', 'TwoFactorEnabled', 'message');
						}
	
					$error = 'Email is associated with another account';
                                        return compact('emails', 'error', 'TwoFactorEnabled');
					}

	
				if(! $saved = $this->save_email($user_id, $email, $details)) {
 				
					//not the best validation, but it's working
					//is the problem that the create() is done within a separate function? 
					$error = 'Email not saved.'; //probably not a valid email address
							 
				} else { $message = "We have sent a validation email to $email, please click the link to finish updating your email";
		
					$email = $current_email; //switch back for display 
					}
			}
		return compact('emails', 'TwoFactorEnabled', 'UnvalidatedEmail', 'message', 'error', 'saved');
	}

	public function verifyemail($user_id, $email, $code) {

	//TODO: think about this
	//Don't need to be logged in to verify email address!!

		$search = Emails::find('first', array(
				'conditions' => array(
						'user_id' => $user_id,
						'Email'   => $email,
						'VerifyCode' => $code)
					));

		if(0 == count($search)) { 
					$error = 'Unable to validate email address';

					return compact('error');
					 }
		
		if($search['Verified'] == true) { 
					$error = 'This email address is already verified';

					return compact('error');
					 }
		
		else { //newly verified emails become the Default
			
			$old = Emails::find('first', array(
					'conditions' => array(
						'user_id' => $user_id,
						'Default' => true)
						));
		
		//for some reason this is preventing the $search->save update below from working on first signup, must be something to do with $search and $old being the same record?
		if( ($old) && ($old['_id'] != $search['_id']) ) $old->save(array('Default' => false));
			
			$search->save(array('Verified' => true, 'Default' => true));


			//update the legacy email record
			Users::find('first', array(
					'conditions' => array(
					       '_id' => $user_id)
					))->save(array('email' => $email), array('validate' => false));

			//in case they just signed up, better set their email status to verified
			Details::find('first', array(
				'conditions' =>array(
					'user_id' => $user_id,
					)
					))->save(array('email.verified' => 'Yes'));

			//log the action
			$log = new ActionLog();
			$log->update_email($user_id, $email);

		$message = 'Your email address has been verified';
	
		return compact('message');;
		}
	}


	public function deleteemail($email_id, $code) {

		//def need be logged in here!!?
		$this->secure();

		$this->delete_email('', $email_id, $code);
	
		return $this->redirect('/settings/profile/');
	}


	public function security() {

		$this->secure();
		$user_id = $this->get_user_id();
		$details = $this->get_details();

		$ga = new GoogleAuthenticator();
	
		if(1 == $details["TOTP.Validate"]) $TwoFactorEnabled = true;
		else	$TwoFactorEnabled = false;

		if(1 == $details['API.Enabled']) $APIEnabled = true;
		else	$APIEnabled = false;


		$key = $details['key'];

			if(! $TwoFactorEnabled) {
			
			$qrcode = $ga->getQRCodeGoogleUrl(COMPANY_URL, $details['secret']);
			}

		/*/////////////////////////////////////////////////////////////////////////////
		//enable / disable 2FA
		////////////////////////////////////////////////////////////////////*/


		if($this->request->data['submit-2fa']) {

		$password = $this->request->data['Password'];
		$code     = $this->request->data['2FA'];

			if(($this->request->data['Password'] == '') OR
			   ($this->request->data['2FA'] == '') ) {
			
			$error_2fa = 'All fileds are required';
			return compact('error_2fa', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
			}
			

				 if($key != $this->request->data['key']) {

                       		 $error_2fa = 'Settings not updated';
                        	 return compact('error_2fa', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
                       		 }

	
				//check the code
				if(! $ga->verifyCode($details['secret'], $code, 1)) {

				$error_2fa = 'Invalid Two Factor Code';
				return compact('error_2fa', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
				}
			
				
				//check password				
				if(! $this->validate_password($user_id, $password) ) {

					$error_2fa = 'Password is incorrect.';
					return compact('error_2fa', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
				}
			
			//we are good - enable  or disable
			if($TwoFactorEnabled) {

				//create new secret
				$secret = $ga->createSecret();

				$details->save(array('TOTP.Validate' => '', 'secret' => $secret));
				
				$TwoFactorEnabled = false;	
				
				$qrcode = $ga->getQRCodeGoogleUrl(COMPANY_URL, $secret);

				$message_2fa = 'Two Factor Authentication is now disabled';
				
				$log = new ActionLog();
				$log->disabled_2fa($user_id);
		
			} else {

				$details->save(array('TOTP.Validate' => '1'));

				$TwoFactorEnabled = true;	

				//need to write OTPVerified = true to the session
				$user = Session::read('default');
				$user['OTPVerified'] = true;
				Session::write('default', $user);

	
				$message_2fa = 'Two Factor Authentication is now enabled';

				$log = new ActionLog();
				$log->enabled_2fa($user_id);
			}

					return compact('message_2fa', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
		}

		/*/////////////////////////////////////////////////////////////////////////////
		//update password
		////////////////////////////////////////////////////////////////////*/
		if($this->request->data['submit-password']) {

			if(($this->request->data['OldPassword'] == '') OR
			   ($this->request->data['NewPassword'] == '') OR
			   ($this->request->data['ConfirmPassword'] == '')) {

			$error = 'All fields are required';
			return compact('error', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
			}


			if($key != $this->request->data['key']) {

			$error = 'Password not changed';
			return compact('error', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
			} 


				//do passwords match?
				if($this->request->data['NewPassword'] != $this->request->data['ConfirmPassword']) {
				
				$error = 'New password fields do not match';
				return compact('error', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
				}
				
					//check 2fa
					if($TwoFactorEnabled) {
						
				
						if(! $ga->verifyCode($details['secret'], $this->request->data['2FA'], 2)) {

						$error = 'Invalid Two Factor Code';
						return compact('error', 'TwoFactorEnabled', 'key', 'APIEnabled');
						}
	
					} //end 2fa

						//check password
						if(! $this->validate_password($user_id, $this->request->data['OldPassword']) ) {


						$error = 'Current password is incorrect.';
						return compact('error', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
						}
			

			//must be good, update
			if(! $this->update_password($user_id, $this->request->data['NewPassword'])) {

			$error = 'Unable to update password';
			return compact('error', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');
			}			

			$message = 'Your password has been updated.';
			return compact('message', 'TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled');			

			}//change passd submitted

	return compact('TwoFactorEnabled', 'key', 'qrcode', 'APIEnabled', 'invalid_addresses');
}

	public function api() {

	$this->secure();
	$details = $this->get_details();
	$user_id = $this->get_user_id();

	$addresses['BTC'] = $details['API.AddressesBTC'];
	$addresses['CC'] = $details['API.AddressesCC'];
	

		$ga = new GoogleAuthenticator();
	
		if(1 == $details["TOTP.Validate"]) $TwoFactorEnabled = true;
		else	$TwoFactorEnabled = false;

		if(1 == $details['API.Enabled']) $APIEnabled = true;
		else	$APIEnabled = false;

			//POST received
		      if($this->request->data['submit-api']) {

			//check passwd
                        if( ($this->request->data['Password'] == '') OR (! $this->validate_password($user_id, $this->request->data['Password']) )) {

                        $error = 'Invalid password';
                        }
			
			//check 2fa
                        elseif($TwoFactorEnabled) {


                               if(! $ga->verifyCode($details['secret'], $this->request->data['2FA'], 2)) {

                               $error = 'Invalid Two Factor Code';
                               }

			}

		if(isset($error)) {

                        return compact('error', 'TwoFactorEnabled', 'APIEnabled', 'addresses');
		}
		
		//passed security, what do we want to do?
		$api_action = $this->request->data['api_action'];
		
		if($api_action == 'enable_disable') {

			if($APIEnabled) { 
				$data = array('API.Enabled' => '0');
				$APIEnabled = false;
				$message = 'API has been disabled';
			}
			else {
				$data = array('API.Enabled' => '1');
				$APIEnabled = true;
				$message = 'API has been enabled';
			     }			
 
		$details->save($data);
		return compact('message', 'TwoFactorEnabled', 'APIEnabled', 'addresses');
	
		}
		elseif($api_action == 'new_credentials') {

			$creds = $this->generate_credentials();

			$api_key = $creds['key'];
			$api_secret = $creds['secret'];

			$data = array('API.Key' => $api_key, 'API.Secret' => $api_secret);
			$details->save($data);

			$message = "New API credentials generated";

		return compact('message', 'TwoFactorEnabled', 'APIEnabled', 'api_key', 'api_secret', 'addresses');

		}
		elseif($api_action == 'view_credentials') {

			$api_key = $details['API.Key'];
			$api_secret = $details['API.Secret'];

		return compact('TwoFactorEnabled', 'APIEnabled', 'api_key', 'api_secret', 'addresses');

		}
		elseif($api_action == 'update_withdrawals') {

		if($this->request->data['include_alt_address']) $include_alt = true;
		if($this->request->data['include_existing']) $include_existing = true;

			//validate the addresses
		         foreach(explode("\r", $this->request->data['Addresses']) as $address) {

			$address = trim($address);
				
			if('' == $address) continue;

				if($address[0] == 'a') $address_type = 'CC';
				else $address_type = 'BTC';

                                if(! $addr = Coinprism::validate_address($address) ) { 

				$invalid_addresses[$address_type][] = $address;  
				}
          
				else {
					 
					$valid_addresses[$address_type][] = $address; 
					 
					 	if($include_alt) {
						if('BTC' == $address_type) $valid_addresses['CC'][] = $addr['asset_address'];
						else $valid_addresses['BTC'][] = $addr['btc_address']['address'];
					 	} 
					}
                         }

			
			if($include_existing) {

			$valid_addresses['BTC'] = array_unique(array_merge( (array) $valid_addresses['BTC'], (array) $addresses['BTC']));
			$valid_addresses['CC'] = array_unique(array_merge( (array) $valid_addresses['CC'], (array) $addresses['CC']));
			}
				
				$data = array('API' => array(
						'AddressesBTC' => $valid_addresses['BTC'],
						'AddressesCC' => $valid_addresses['CC'],	
					    ));	
                
					 
				$details->save($data);

			$message = "Your withdrawal addresses have been updated.";
			
			if(0 != count($invalid_addresses)) {

			$error .= "<p>The following addresses are invalid and have been excluded:</p>";

			foreach($invalid_addresses['BTC'] as $invalid) {

			$error .= "BTC: $invalid<br />";
			}
			foreach($invalid_addresses['CC'] as $invalid) {

			$error .= "CC: $invalid<br />";
			}
			}

		$addresses = $valid_addresses;

		return compact('message', 'TwoFactorEnabled', 'APIEnabled', 'addresses', 'error');
		}//else/if
	
	}
		return compact('TwoFactorEnabled', 'APIEnabled', 'addresses');
}
	public function notifications() {

	return;
	}

	
	public function identity() {

	return;
	}

/*//////////////////////////////////////////////
           Email helper functions 
//////////////////////////////////////////////*/

	/*
	Save a new email address as unverified
	*/
	private function save_email($user_id, $email, $details) {

		$verify_code = sha1($user_id . $email);
		$delete_code = sha1($email . $user_id . time());

		$data = Emails::create(array(
				'user_id'    => $user_id,
				'Email'      => $email,
				'VerifyCode' => $verify_code,
				'Verified'   => false,
				'Default'     => false,
				'DeleteCode' => $delete_code,
				))->save();
		
		if(!$data) { return false; }

		else {  //send validation emails
			
			$view  = new View(array(
				'loader' => 'File',
				'renderer' => 'File',
				'paths' => array(
					'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
				)
			));

			$body = $view->render(
				'template',
				compact('user_id', 'email','verify_code','delete_code', 'details'),
				array(
					'controller' => 'settings',
					'template'=>'ValidateEmail',
					'type' => 'mail',
					'layout' => false
				)
			);

			$transport = Swift_MailTransport::newInstance();
			$mailer = Swift_Mailer::newInstance($transport);
	
			$message = Swift_Message::newInstance();
			$message->setSubject("Please verify your new ".COMPANY_NAME." email address");
			$message->setFrom(array(NOREPLY => COMPANY_NAME));
			$message->setTo($email);
			$message->setBody($body,'text/html');
			
			$mailer->send($message);
			
		 return $data;

		    }
	}

/*
	Can be used for an individual to delete a non verified email.
	Or, used when an email becomes validated, to delete all other unvalidated instances.

	If the last 3 
*/
	private function delete_email($email = false, $email_id = false, $delete_code = false) {

		if(($delete_code) && ($email_id)) {

                        $search = Emails::find('first', array(
                                'conditions' => array(
                                               '_id' => $email_id,
                                                'DeleteCode' => $delete_code,
						'Verified' => false,
                                                'Default' => false,
                                               ) ))->delete();
		}elseif($email) {

			$search = Emails::find('all', array(
				'conditions' => array(
						'Email' => $email,
						'Verified' => false,
						'Default' => false)
						))->delete();
		}
	}

	private function make_email_default($user_id, $email) {
		
		$new = Emails::find('first', array(
				'conditions' => array(
					'user_id' => $user_id,
					'Email'   => $email,
					'Verified'=> true)
				));
	
		if(0 == count($new)) return $this->redirect('/settings/profile/'); //invalid email, maybe not verified yet?
	
		$old = Emails::find('first', array(
				'conditions' => array(
					'user_id' => $user_id,
					'Verified'=> true,
					'Default' => true)
				));

		$old->save(array('Default' => false)); 
		$new->save(array('Default' => true)); 
	}

	/*
		Generate a new key and secret
		returns (array) key and secret
	*/
	private function generate_credentials() {

                 $ga = new GoogleAuthenticator();

		$array = array('key'=>$ga->createSecret(64),
			       'secret'=>$ga->createSecret(64));

		return $array;
	}
}

?>
