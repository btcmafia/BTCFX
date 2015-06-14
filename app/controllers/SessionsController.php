<?php
namespace app\controllers;

use lithium\security\Auth;
use lithium\util\String;
use app\models\Users;
use app\models\Pages;
use app\models\Logins;
use app\models\FailedLogins;
use app\models\Details;
use lithium\storage\Session;
use app\extensions\action\Functions;
use app\extensions\action\ActionLog;
use app\extensions\action\GoogleAuthenticator;

class SessionsController extends \app\extensions\action\Controller {

    public function add($flag = null) {
			//perform the authentication check and redirect on success
			
			Session::delete('default');				
			$response = file_get_contents("http://ipinfo.io/{$_SERVER['REMOTE_ADDR']}");
			$IPResponse = json_decode($response);

			if($IPResponse->tor) {

			$error = "Unfortunately, we do not allow known TOR IP addresses.";

		    // Display error message or something
					Auth::clear('member');
					Session::delete('default');
					return false;
			}

		$ip_address = $_SERVER['REMOTE_ADDR'];

		//check for excessive login attempts

		//currently allow 10 failed logins in a 15 minute period from the same IP.

		$time_limit = time() - (60 * 15);

		$logins = FailedLogins::find('all', array(
					'conditions' => array(
						'username' => $this->request->data['username'],
						'ip_address' => $ip_address,
						'Timestamp' => array('>=' => $time_limit),
						)
				));

		if(10 <= count($logins)) {

		$error = "Too many failed login attempts, please try again in 15 minutes.";

			Auth::clear('member');
                        Session::delete('default');
                        return compact('error');
		}


			if (Auth::check('member', $this->request)){
				//Redirect on successful login
				$loginpassword = $this->request->data['loginpassword'];
				$default = Auth::check('member', $this->request);
				$details = Details::find('first',array(
					'conditions' => array(
						'username'=>$default['username'],
						'user_id'=>(string)$default['_id']
						)
				));
				if($details['active']=="No"){
					Auth::clear('member');
					Session::delete('default');
					return $this->redirect('/');
					exit;
				}

					$data = array(
						'oneCodeused'=>'No',
						'lastconnected'=>array(									
									'IP' => $ip_address,
									'ISO'=> $IPResponse->country,
									'hostname'=> $IPResponse->hostname,
									'city'=> $IPResponse->city,
									'region'=> $IPResponse->region,									
									'loc'=> $IPResponse->loc,
									'org'=> $IPResponse->org,									
									'postal'=> $IPResponse->postal,									
									'DateTime' => new \MongoDate(),
								)
					);
					$details = Details::find('first',array(
						'conditions' => array(
							'username'=>$default['username'],
							'user_id'=>(string)$default['_id']
							)
					))->save($data);

					$details = Details::find('first',array(
						'conditions' => array(
							'username'=>$default['username'],
							'user_id'=>(string)$default['_id']
							)
					));

			//Successful login means we delete all the failed login attempts from this IP
			$logins = FailedLogins::find('all', array(
						'conditions' => array(
							'username' => $details['username'],
	                                                'ip_address' => $ip_address,
						)
					));

			if(0 != count($logins)) $logins->delete();


				//log them in
				Session::write('default',$default);
				$user = Session::read('default');
					

					$user_id = $default['_id'];
					$metadata = (array) $IPResponse;
					$protocol = 'web';

					$log = new ActionLog();
					$log->login($user_id, $metadata, $protocol);

						return $this->redirect('in::splash');
						exit;
					}



			//if theres still post data, and we weren't redirected above, then login failed
			if($this->request->data){

			//record failed login attempt
			$logins = FailedLogins::create();
		
			$data = array(
					'username' => $this->request->data['username'],
	                                'ip_address' => $ip_address,
                                        'Timestamp' => time(),
				); 

			$logins->save($data);

			$error = "Login failed.";
			}
	

		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>'login')
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];

		if('1' == $flag) $message = "Your password has been updated.";

			return compact('title','keywords','description', 'error');
			return $this->redirect('/');
			exit;
    }

	 public function delete() {

		//we would like to log the logout event, so we'll grab their user id
		$this->secure();
		$user_id = $this->get_user_id();

		Auth::clear('member');
		Session::delete('default');

		$log = new ActionLog();
		$log->logout($user_id);

		return $this->redirect('/');
		exit;
    }
}
?>
