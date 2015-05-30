<?php
namespace app\extensions\action;
use lithium\storage\Session;
use app\models\Details;
use app\models\Emails;


class Controller extends \lithium\action\Controller {

	private $details;
	private $email;
	private $email_verified;
	public  $user_id;

    protected function _init() {
        parent::_init();

	}

   public function secure($level = false) {
	
	if( ('admin' == $level) && (! $this->is_admin()) ) return $this->redirect(PROTOCOL . '://' . COMPANY_URL);


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

  public function get_user_id() {

	return $this->user_id;
	}

  public function get_details() {

	return $this->details;

	}

  public function get_email() {

	return $this->email;

	}

  public function is_admin() {

  return false; // no admin yet!!
  }
}

?>
