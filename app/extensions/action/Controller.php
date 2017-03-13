<?php
namespace app\extensions\action;

use lithium\storage\Session;
use app\models\Details;
use app\models\Emails;
use app\models\Users;
use app\models\ActiveData;
use app\controllers\ApiController;
use lithium\util\String;

class Controller extends \lithium\action\Controller {

	private $details;
	private $email;
	private $email_verified;
	private $active_data;
	public  $user_id;
	public $username;
	public $is_staff;
	public $is_admin;
	public $is_contractor;


	public $security_done = '';


	public $count;

    protected function _init() {
        parent::_init();

	}

/*
  make some common variables available to the layout (and all views)

  see: http://www.jblotus.com/2011/08/31/using-an-appcontroller-in-lithium-php-to-pass-data-to-the-layout/
*/
public function render($options = array()) {
  //  $auth = Auth::check('default');
	$username = $this->get_username();

	$is_staff = $this->is_staff();
	$is_contractor = $this->is_contractor();
	$is_admin = $this->is_admin();

    $this->set(compact('username', 'is_staff', 'is_contractor', 'is_admin'));
    parent::render($options);
  }

/*
	Check the user is logged in and make several functions available.
	@param $level (string) admin | api. If api then security is already done, so we just populate the $details and $user_id fields for later use
	@param $data (array) If $level = api then this will be the $details field that needs storing
*/
   public function secure($level = false, $details = false) {

	if('office' == $level) $level = 'staff'; //just an alias
	

		//if it's an api call then we don't create the session below, authentication has already been done
		//use the $details passed
		if( ('api' == $level) && ($details['user_id']) ) {

		$this->details = $details;
		$this->user_id = $details['user_id'];
	
		return;
		}

	$active_data = $this->get_active_data();
	
        if('' == $active_data['user_id']) return $this->redirect('/login/');

	   
	$user_id = $active_data['user_id'];
	$this->user_id = $user_id;
	$this->username = $active_data['username'];

	if(true == $active_data['permissions']['office']) $this->is_staff = true;
	if(true == $active_data['permissions']['contractor']) $this->is_contractor = true;
	if(true == $active_data['permissions']['admin']) $this->is_admin = true;

	if( ('admin' == $level) && (! $this->is_admin()) ) return $this->redirect(PROTOCOL . '://' . COMPANY_URL);
	if( ('staff' == $level) && (! $this->is_staff()) ) return $this->redirect(PROTOCOL . '://' . COMPANY_URL);
	

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


  public function get_active_data($key = false) {

	$cookie_id = Session::read('default');

        $active_data = ActiveData::find('first', array(
                                'conditions' => array(
                                        '_id' => $cookie_id
                                        )
                                ));
 
	if($key) return $active_data[$key];

	else return $active_data;
   }


  public function update_active_data($data) {

	$active_data = $this->get_active_data();

	$active_data->save($data);

	return;
  }


	public function validate_password($user_id, $password) {

		 $users = Users::find('first',array(
                                 'conditions' => array(
                                       '_id' => (string) $user_id,
                                       'password' => String::hash($password)),
                                      ));

                if(0 == count($users)) return false;

		else return true;
	}

	public function update_password($user_id, $password) {
               
		$users = Users::find('first',array(
                                 'conditions' => array(
                                       '_id' => $user_id,
                                       )
					));

                if(0 == count($users)) return false;


		$passwd = String::hash($password);
                $data = array('password' => $passwd);
                        
                $users->save($data, array('validate' => false)); 

                //record the action
                $log = new ActionLog();
                $log->update_password($user_id, $passwd);

		return true;
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
                                'user_id' => $this->user_id,
                                'Default' => true)
                        ));

	$this->email = $email['Email'];
	}

	return $this->email;

	}

 public function get_username() {

	return $this->username;
 }

  public function is_admin() {

  return $this->is_admin;
  }

  public function is_staff() {

  return $this->is_staff;  
  }

 public function is_contractor() {

 return $this->is_contractor;
 }

 public function get_markets() {

 global $markets;
 return $markets;
 }

 public function get_periods() {

 global $periods;
 return $periods;
 }


/*
	Takes an array containing the parts of an address and returns as a string

	Todo: check for empty fields
*/
 public function format_address($address, $format = 'text') {

	if('text' == $format) {

		$result = "{$address['address_1']}, {$address['address_2']}, {$address['city']}, {$address['postcode']}";
	}
	elseif('html' == $format) {

	 	$result = "{$address['address_1']}<br />{$address['address_2']}<br />{$address['city']}<br />{$address['postcode']}";
	}

	return $result;
 }


 public function validate_currency($currency) {

 global $currencies;
 $currency = strtolower($currency);

 if(! in_array($currency, $currencies)) return false; 

 else return $currency;
 }

public function period_details($period, $option = false) {

	if($option == 'start_time') {

        if('Morning' == $period) return '9am';
        if('Lunchtime' == $period) return '12pm';
        if('Afternoon' == $period) return '3pm';
        if('Evening' == $period) return '6pm';

	}

	if($option == 'end_time') {

        if('Morning' == $period) return '12pm';
        if('Lunchtime' == $period) return '3pm';
        if('Afternoon' == $period) return '6pm';
        if('Evening' == $period) return '9pm';

	}


        if('Morning' == $period) return '(9am - 12pm)';
        if('Lunchtime' == $period) return '(12pm - 3pm)';
        if('Afternoon' == $period) return '(3pm - 6pm)';
        if('Evening' == $period) return '(6pm - 9pm)';

        return '';
  }


}

?>
