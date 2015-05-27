<?php
namespace app\extensions\action;

class Security extends \lithium\action\Controller {

	public $user_id;
	public $username;
	public $email;
	public $is_admin;
	public $email_verified;
	public $otp_verified;


	public __construct() {

		$user = Session::read('default');
		if ($user==""){		return $this->redirect('/login');}
		
		$this->$user_id = $user['_id'];

		$details = Details::find('first',
			array('conditions'=>array('user_id'=>$user_id))
		);
	}
}

?>
