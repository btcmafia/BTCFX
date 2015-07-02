<?php
namespace app\extensions\action;
use lithium\storage\Session;
use app\models\Details;
use app\models\Emails;
use app\models\Users;
use app\controllers\ApiController;
use lithium\util\String;

class Controller extends \lithium\action\Controller {

	private $details;
	private $email;
	private $email_verified;
	public  $user_id;
	public $security_done = '';

	public $count;

    protected function _init() {
        parent::_init();

	}


/*
	Check the user is logged in and make several functions available.
	@param $level (string) admin | api. If api then security is already done, so we just populate the $details and $user_id fields for later use
	@param $data (array) If $level = api then this will be the $details field that needs storing
*/
   public function secure($level = false, $details = false) {

	
	if( ('admin' == $level) && (! $this->is_admin()) ) return $this->redirect(PROTOCOL . '://' . COMPANY_URL);

		//if it's an api call then we don't create the session below, authentication has already been done
		//use the $details passed
		if( ('api' == $level) && ($details['user_id']) ) {

		$this->details = $details;
		$this->user_id = $details['user_id'];
	
		return;
		}

 	$user = Session::read('default');
        if ($user==""){   return $this->redirect('/login');
		}
	   
	$user_id = $user['_id'];
	$this->user_id = $user_id;


	$this->details = Details::find('first', array(
			'conditions' => array(
				'user_id' => $user_id)
			));

	if( ($this->details['TOTP.Validate']) AND (! $user['OTPVerified']) ) {

	return $this->redirect('/in/twofactor');
	}

		$email = Emails::find('first', array(
			'conditions' => array(
				'user_id' => $user_id,
				'Default' => true)
			));

		$this->email = $email['Email'];
		
		if(true != $email['Verified'])  return $this->redirect('/register/thanks/');

	return;
 }

	public function validate_password($user_id, $password) {

		 $users = Users::find('first',array(
                                 'conditions' => array(
                                       '_id' => $user_id,
                                       'password' => String::hash($password)),
                                      ));

                if(0 == count($users)) return false;

		else return true;
	}

  public function get_user_id() {

	return $this->user_id;
	}

  public function get_details() {	

	return $this->details;
	}

  public function get_email() {

	//if it's an api call then email may not be set 
	if('' == $this->email) {

	 $email = Emails::find('first', array(
                        'conditions' => array(
                                'user_id' => $user_id,
                                'Default' => true)
                        ));

	$this->email = $email['Email'];
	}

	return $this->email;

	}

  public function is_admin() {

  return false; // no admin yet!!
  }

 public function get_markets() {

 global $markets;
 return $markets;
 }

 public function validate_currency($currency) {

 global $currencies;
 $currency = strtolower($currency);

 if(! in_array($currency, $currencies)) return false; 

 else return $currency;
 }
}

?>
