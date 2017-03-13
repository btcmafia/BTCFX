<?php
namespace app\controllers;

use app\extensions\action\OAuth2;
use app\models\Users;
use app\models\Details;
use app\models\Emails; 
use app\models\Invites; 
use lithium\security\Auth;
use lithium\storage\Session;
use app\extensions\action\GoogleAuthenticator;
use lithium\util\String;
use MongoID;


use \lithium\template\View;
use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;

class RegisterController extends \lithium\action\Controller {


	public function index($code = null){
	
	//currently need an invite code


/*
	if(! $code) return $this->redirect('/register/requestinvite');

	//check the code
	$invite = Invites::find('first', array(
				'conditions' => array(
					'invite_code' => $code,
				)
				));

	if(0 == count($invite)) return $this->redirect('/register/requestinvite');

	if('used' == $invite['invited']) $error = 'That invite code has already been used.';

	elseif('yes' != $invite['invited']) $error = 'That invite code is not currently valid.';
*/

	if( (! $error) && ($this->request->data) ) {	
        
		$Users = Users::create($this->request->data);
      		$saved = $Users->save();
		
		if($saved==true){
			
			$ga = new GoogleAuthenticator();
			
			$user_id = (string) $Users->_id;
			$verify_code = sha1($user_id);
			
			$data = array(
				'user_id'=>(string)$Users->_id,
				'username'=>(string)$Users->username,
				'email.verify' => $verify_code,
				'mobile.verified' => "No",				
				'mobile.number' => "",								
				'key'=>$ga->createSecret(64),
				'secret'=>$ga->createSecret(),
				'balance.BTC' => (int)0,
				'balance.TCP' => (int)0,				
				'balance.DCT' => (int)0,				
			);
		
			$details = Details::create()->save($data);
			$email = $this->request->data['email'];
			$name = $this->request->data['firstname'];


			//new way of storing email addresses - as well as the old way
			$emaildata = Emails::create(array(
                                'user_id'    => $user_id,
                                'Email'      => $email,
                                'VerifyCode' => $verify_code,
                                'Verified'   => false,
                                'Default'     => true,
                                ))->save();
			
			$this->send_verification_email($user_id, $email, $verify_code, $Users->username);
       		
			//log them in
			$session = array('_id' => $user_id, 
					'created' => time(), 	
					'email' => $email, 
					'firstname' => $name, 
					'lastname' => $this->request->data['lastname'], 
					'updated' => time(), 
					'username' => $Users->username);

			Session::write('default', $session);
	
			$this->redirect("/register/thanks/$user_id/");	
			
			}
		}	
		return compact('saved','Users', 'error');		
	}

	public function thanks($user_id = false, $resent = false) {

		if(! $user_id) {
			    	$user = Session::read('default');
                		$user_id = $user['_id'];
			}
               	if(! $user_id) {  return $this->redirect('/login');}

		if('resent' == $resent) $message = 'Verification email has been resent.';

		elseif('error' == $resent) $error = 'Failed to resend verification email.'; 


	return compact('user_id', 'message', 'error');
	}

	/*
	Resend the welcome verification email
	*/
	public function resend($user_id) {

		$emails = Emails::find('first', array(
				'conditions' => array(
					'user_id' => $user_id,
					'Default' => true,
					'Verified' => false
					)
				));

		if(1 == count($emails)) {

		$details = Details::find('first', array(
				'conditions' => array(
					'user_id' => $user_id)
				));


		$email = $emails['Email'];
		$verify_code = $emails['VerifyCode'];

		$this->send_verification_email($user_id, $email, $verify_code, $details['username']);

		$this->redirect("/register/thanks/$user_id/resent/");
		}
		else {
			$this->redirect("/register/thanks/$user_id/error/");
			}
	}

	
	private function send_verification_email($user_id, $email, $verify_code, $username) {


	        $view  = new View(array(
                                'loader' => 'File',
                                'renderer' => 'File',
                                'paths' => array(
                                        'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
                                )
                        ));


                $body = $view->render(
                                'template',
                                compact('user_id', 'email','verify_code', 'details'),
                                array(
                                        'controller' => 'register',
                                        'template'=>'ValidateEmail',
                                        'type' => 'mail',
                                        'layout' => false
                                )
                        );



                        $transport = Swift_MailTransport::newInstance();
                        $mailer = Swift_Mailer::newInstance($transport);

                        $message = Swift_Message::newInstance();
                        $message->setSubject("Verification of email from ".COMPANY_URL);
                        $message->setFrom(array(NOREPLY => 'Verification email '.COMPANY_URL));
                        $message->setTo($email);
//                        $message->addBcc(MAIL_1);
//                        $message->addBcc(MAIL_2);
//                        $message->addBcc(MAIL_3);
                        $message->setBody($body,'text/html');

                        $mailer->send($message);

		return;
	}

	public function requestinvite() {

		if($this->request->data) {
		
		if($this->request->data['request_submit']) {

			$invites = Invites::create($this->request->data);
      			$request_invite = $invites->save();
		
		if($request_invite == true){
		
		return $this->redirect('/register/invitethanks/');
		}

		return compact('request_invite');
		}
		
			} elseif($this->request->data['already_submit']) {

			$invite_code = $this->request->data['invite_code'];
			$email = $this->request->data['email'];

			$search = Invites::find('first', array(
						'conditions' => array(
							'email' => $email,
							'invite_code' => $invite_code,
							'invited' => true,
							)
						));

			if(0 != count($search)) {

						 return $this->redirect("/register/index/$invite_code");
						}
			else {
				$error = 'Invitation not found';

				return compact('error', 'already_invite'); }
			}

	}

	public function invitethanks() {

	return;
	}

}

?>
