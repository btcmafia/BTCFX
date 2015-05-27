<?php

namespace app\controllers;

class VerificationController extends \lithium\action\Controller {

	public function index() {
		return $this->render(array('layout' => false));
	}



	public function emailconfirm($email, $code) {

		$user = Session::read('member');
		$id = $user['_id'];

		$details = Details::find('first',
			array('conditions'=>array('user_id'=>$id))
		);

		if(isset($details['email']['verified'])){
			$msg = "Your email is verified.";
		}else{
			$msg = "Your email is <strong>not</strong> verified. Please check your email to verify.";
			
		}
		$title = "Email verification";
		return compact('msg','title');

		if($this->request->data) {

		if(
		}	
	}
