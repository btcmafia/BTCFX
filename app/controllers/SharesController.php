<?php
namespace app\controllers;

use app\models\Users;
use app\models\Details;
use app\models\Shares;
use lithium\storage\Session;
use app\extensions\action\Functions;

use \lithium\template\View;
use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;

class SharesController extends \lithium\action\Controller {

	public function index(){
	}
	public function x($currency = null){
		if($currency==null){$this->redirect(array('controller'=>'ex','action'=>'dashboard/'));}
		$currencies = split("_",$currency);
		$first_curr = strtoupper($currencies[0]);
		$second_curr = strtoupper($currencies[1]);

		$title = $first_curr . "/" . $second_curr;
		$user = Session::read('member');
		$id = $user['_id'];
		
		$company = Details::find('first',array(
			'conditions'=>array(
				'company.ShortName' => $second_curr
			)
		));
		
		$details = Details::find('first',array(
			'conditions'=>array(
				'user_id' => $id
			)
		));
		if($company['user_id']==$details['user_id']){
			$this->redirect('Users::addcompanydetail');	
		}
		return compact('title','details','company','first_curr','second_curr');

	}

}
?>