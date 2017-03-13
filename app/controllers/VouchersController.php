<?php
namespace app\controllers;

use lithium\storage\Session;
use app\models\Vouchers;


class VouchersController extends \app\extensions\action\Controller {

        public function index() {

        $user = Session::read('default');

	if($user=='') return $this->redirect('/login/');

        return( compact('user') );
        }


	public function create() {

        $user = Session::read('default');

	if($user['permissions']['admin']!=true) return $this->redirect('/login/');
		
	$user_id = $user['_id'];
	
		if($this->request->data) {

			$value = $money->undisplay_money($this->request->data['value'],'TCP');

			if('' == $this->request->data['voucher_type']) $voucher_type = 'transferable';
			else $voucher_type = $this->request->data['voucher_type'];

			if('' == $this->request->data['issued_to']) $issued_to = '';
			else $issued_to = $this->request->data['issued_to'];

			if( ('' == $issued_to) && ('transferable' != $voucher_type)) {

				$error = 'Non transferable vouchers need to be issued to a specific person';
			
			return compact('error');
			} 

			//Note: we don't validate the email address here because recipient does not have to be a user.	

			$voucher = Vouchers::create();

			$ga = new GoogleAuthenticator();
			$voucher_key = $ga->createSecret(32);

			$data = array(
					'created_by' => $user_id,
					'voucher_type' => $voucher_type,
					'issued_to' => $issued_to,
					'voucher_key' => $voucher_key,
					'value' => $value,
					'currency' => 'TCP',
					'used' => false,
					);

			$voucher->save($data);

			$message = 'Voucher has been generated';
	
			return compact('message');
		}

	}

	public function verify() {

		if($this->request->data) {

			$search = Vouchers::find('first', array(
					'conditions' => array(
						'voucher_key' => $this->request->data['voucher_key'],
						)
					));

			if(0 == count($search)) {

				$error = 'Voucher not found';
				return compact('error');
			}

			if($search['transferable'] != true) {

				if($search['issued_to'] != $this->request->data['issued_to']) {

					$error = 'The voucher was not issued to this email address and is not transferable';
					return compact('error');
				}
			}

			$money = new Money();
			$value = $money->display_money($search['value']);

			$voucher_id = $search['voucher_id'];
			$redeem_key = $search['redeem_key'];

			$message = "The voucher is valid. The value is $value."; 

			return compact('voucher_id', 'redeem_key');
		}

	}
}



?>
