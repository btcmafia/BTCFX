<?php
namespace app\controllers;

use app\models\Contractors;
use app\models\Timeslots;
use app\models\Users;
use app\models\Services;
use app\models\Details;
use app\models\Emails; 
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\Money;
use lithium\util\String;
use MongoID;
use lithium\storage\Session;

use \lithium\template\View;
use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;

class AdminController extends \app\extensions\action\Controller {


	public function addservices() {

	/*
	Was used with data in resources/services.php to install services in DB
	*/


/*
	foreach($service as $data) {
		Services::create()->save($data);
	}
	return 'Services Installed';
*/
	return 'Method not active. See controller for details.';

	}
	

	public function createservice() {

        $user = Session::read('default');
        if ($user['permissions']['admin']==""){ return $this->redirect('/login'); }


	return compact('details');
	}

	public function viewservices($trade) {

     $user = Session::read('default');
        if ($user['permissions']['office']==""){ return $this->redirect('/login'); }


	return compact('details');
	}

	public function editservice($trade) {

     $user = Session::read('default');
        if ($user['permissions']['admin']==""){ return $this->redirect('/login'); }


	return compact('details');
	}


	
	public function contractorlist($trade = false) {

        $user = Session::read('default');
        if ($user['permissions']['office']==""){ return $this->redirect('/login'); }


	$this->secure();

	



	$conditions = array('active' => true);

		$contractors = Contractors::find('all', array(
					'conditions' =>	$conditions
						));

	return compact('contractors');	

	}

	public function contractorservices($user_id) {

     $user = Session::read('default');
        if ($user['permissions']['admin']==""){ return $this->redirect('/login'); }


	$this->secure();


				
				$contractor = Contractors::find('first', array(
					'conditions' =>	array(
						'user_id' => $user_id) 
						));
	
				if(0 == count($contractor)) die('Invalid contractor!');	
	
		                $all_services = Services::find('all', array(
                                        'conditions' => array(
						'active' => true)
                                                ));

		if($this->request->data) {
				
			foreach($all_services as $foo) {

			$service_id = (string) $foo['_id'];

			if(isset($this->request->data[$service_id])) {

			$data['services'][$foo['service_name']] = array('service_id' => $service_id, 'allowed' => true, 'trade' => $foo['trade'], 'service_category' => $foo['service_category']);  
			}	
			else {

			$data['services'][$foo['service_name']] = array('service_id' => $service_id, 'allowed' => false, 'trade' => $foo['trade'], 'service_category' => $foo['service_category']);  
			}


                	} //foreach 

			$contractor->save($data);

			$message = 'Updated';

		} //end form submitted

	$money = new Money();

/*

		$conditions = array(
				'user_id' => $user_id,
				);

		$contractor = Contractors::find('first', array(
					'conditions' =>	$conditions
						));



		$conditions = array(
				'active' => true,
				);

		$all_services = Services::find('all', array(
					'conditions' =>	$conditions
						));
*/
		$trading_name = $contractor['trading_name'];
		
		$trades = array();
		$service_categories = array();
		$i = 0;

		foreach($all_services as $foo) {

		$trade = $foo['trade'];
		$service_category = $foo['service_category']; 

		if(! in_array($service_category, $service_categories) ) $service_categories[] = $service_category; 
		if(! in_array($trade, $trades) ) $trades[] = $trade; 

			$services[$trade][$service_category][$i]['service_id'] = (string) $foo['_id'];
			$services[$trade][$service_category][$i]['service_name'] = $foo['service_name'];
			
		if($contractor['services'][$foo['service_name']]['allowed'] == true) {

			$services[$trade][$service_category][$i]['allowed'] = true;
			$services[$trade][$service_category][$i]['active'] = $contractor['services']['service_name']['active'];
			//$services[$trade][$service_category][$i]['min_rate'] = $money->display_money($contractor['services']['service_name']['min_rate'], 'TCP');
		} 
		
			$i++;

		} //foreach 



	return compact('services', 'trades', 'service_categories', 'contractor', 'trading_name', 'message');
	}


	public function newcontractor(){

     $user = Session::read('default');
        if ($user['permissions']['admin']==""){ return $this->redirect('/login'); }

	$this->secure();
	
	if( (! $error) && ($this->request->data) ) {	
        
		$Users = Users::create();

		$username = strtolower($this->request->data['username']); //lowercase only 
		$firstname = $this->request->data['firstname']; 
		$lastname = $this->request->data['lastname']; 
		$email = $this->request->data['email'];
		
		$permissions['admin'] = false;
		$permissions['office'] = false;
		$permissions['contractor'] = true;


		$data = array(
			'username' => $username,
			'firstname' => $firstname,
			'lastname' => $lastname,
			'email' => $email,
			'permissions' => $permissions
			);

      		$saved = $Users->save($data);
		
		if($saved==true){
			
			$ga = new GoogleAuthenticator();
			
			$new_user_id = (string) $Users->_id;
			$verify_code = sha1($user_id);
			
                //generate a password reset key
                $ga = new  GoogleAuthenticator();
                $key = $ga->createSecret(64);
                $expiry = time() + 60 * 60 *24 * 7; //7 days


			$data = array(
				'user_id'=>(string)$Users->_id,
				'username'=>(string)$Users->username,
				'email.verified'=> "Yes",
				'email.verify' => $verify_code,
				'mobile.verified' => "Yes",				
				'mobile.number' => $this->request->data['mobile'],
				'PasswordReset.Key' => $verify_code,
				'PasswordReset.Expiry' => $expiry,								
				'key'=>$ga->createSecret(64),   //not sure we still need this?
				'secret'=>$ga->createSecret(20), //or this
				'balance.BTC' => (int)0,
				'balance.TCP' => (int)0,				
				'balance.DCT' => (int)0,				
			);
		
			$details = Details::create()->save($data);
			$email = $this->request->data['email'];
			$name = $this->request->data['firstname'];

			$trading_name = $this->request->data['trading_name'];

			//new way of storing email addresses - as well as the old way
			$emaildata = Emails::create(array(
                                'user_id'    => $new_user_id,
                                'Email'      => $email,
                                'VerifyCode' => $verify_code,
                                'Verified'   => true,
                                'Default'     => true,
                                ))->save();
			
			//$this->send_verification_email($user_id, $email, $verify_code, $Users->username);
       	
			//Make the user a contractor
			$contractor = Contractors::create();

			$default_jobs_per_period = 0;
			$default_jobs_per_period_evening = 0;
			$default_max_jobs_per_period = 3;
		
			$default_rate = '4750';
			$default_rate_evening = '7000';	

			$contractor_data = array(
				'user_id' => $new_user_id,
				'trading_name' => $trading_name,
				'active' => true,

				'default_jobs_per_period.Morning' => $default_jobs_per_period,
				'default_jobs_per_period.Lunchtime' => $default_jobs_per_period,
				'default_jobs_per_period.Afternoon' => $default_jobs_per_period,
				'default_jobs_per_period.Evening' => $default_jobs_per_period_evening,

				'default_max_jobs_per_period.Morning' => $default_max_jobs_per_period,
				'default_max_jobs_per_period.Lunchtime' => $default_max_jobs_per_period,
				'default_max_jobs_per_period.Afternoon' => $default_max_jobs_per_period,
				'default_max_jobs_per_period.Evening' => $default_max_jobs_per_period,

				'default_rate.Morning' => $default_rate,
				'default_rate.Lunchtime' => $default_rate,
				'default_rate.Afternoon' => $default_rate,
				'default_rate.Evening' => $default_rate_evening,
				);


			$contractor->save($contractor_data);


			//create the timeslots

		for($count = 0; $count < 3; $count++) {

			$time_now = time();		
			$time_period = $time_now + ($count * 60*60*24);

		         $periods = array('Morning', 'Lunchtime', 'Afternoon', 'Evening');

			foreach($periods as $period) {

                        $data = array(
				'user_id' => $new_user_id,
				'period.day' => date('z', $time_period) + 1, //php date function counts days from zero
				'period.week' => date('W', $time_period),
				'period.year' => date('Y', $time_period),
                                'period_nicename' => $period,
                                'slots_available' => $contractor_data["default_jobs_per_period.$period"],
                                'default_max' => $contractor_data["default_max_jobs_per_period.$period"],
                                'rate' => $contractor_data["default_rate.$period"],
                                'edited' => false
                                );

			$timeslot = Timeslots::create();
                        $timeslot->save($data);

			} //end foreach
		} //end for while 

		$this->send_contractor_welcome_email($new_user_id, $username, $email, $verify_code, $expiry, $firstname, $lastname, $trading_name);
		
		$message = "Contractor created. Don't forget to approve them for some services.";

			

			//return $this->redirect("/newcontractor/completed/$new_user_id/");	
			
		}
		}	
		return compact('message', 'error');		
	}

	public function completed($new_user_id = false) {

     $user = Session::read('default');
        if ($user['permissions']['admin']==""){ return $this->redirect('/login'); }

		if(! $new_user_id) { $error = 'Error creating contractor'; } 

		else $message = 'New contractor created';

	return compact('message', 'error');
	}

	
	private function send_contractor_welcome_email($new_user_id, $username, $email, $verify_code, $expiry, $firstname, $lastname, $trading_name) {




	        $view  = new View(array(
                                'loader' => 'File',
                                'renderer' => 'File',
                                'paths' => array(
                                        'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
                                )
                        ));


                $body = $view->render(
                                'template',
                                compact('new_user_id', 'username', 'email','verify_code', 'firstname', 'lastname', 'trading_name'),
                                array(
                                        'controller' => 'admin',
                                        'template'=>'ContractorWelcome',
                                        'type' => 'mail',
                                        'layout' => false
                                )
                        );



                        $transport = Swift_MailTransport::newInstance();
                        $mailer = Swift_Mailer::newInstance($transport);

                        $message = Swift_Message::newInstance();
                        $message->setSubject("Contractor account at ".COMPANY_NAME);
                        $message->setFrom(array(NOREPLY => COMPANY_NAME));
                        $message->setTo($email);
                        $message->setBody($body,'text/html');

                        $mailer->send($message);

		return;
	}



}

?>
