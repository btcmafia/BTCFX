<?php

namespace app\controllers;
use app\extensions\action\Withdraw;

class WithdrawController extends \app\extensions\action\Controller {

        public function index() {

	$foo = new Withdraw(COINPRISM_USERNAME, COINPRISM_PASSWORD);

	$tcp_id = 'AHUSvtMaqQEStWH1nnamMubQpQ7X4AnTHu';

	//$inputs = array('156b3qYx8QNJShrn5tJ9aG8M1mCVYLA5jN', '1JRTsdksvwkfU3UT5gdshuNutY6aJND2RN'); //5 & 6
	$inputs = array('1zLkEoZF7Zdoso57h9si5fKxrKopnGSDn'); //5 & 6

	$outputs = array(
			array('address' => 'akUxi6uhMEWhHUbaBniso7nFFao5c98GbPN', 'amount' => '7', 'asset_id' => $tcp_id),
			//array('address' => 'akJJTn2w4ozjQCigomiEJ2Ngf3cBr5uUGiQ', 'amount' => '8', 'asset_id' => $tcp_id),
			//array('address' => 'akWV1fUWWv7uACgAA8bDibqg29WChhaPD37', 'amount' => '9', 'asset_id' => $tcp_id),
			);
       
	$result = $foo->send_asset($inputs, $outputs);
 
	return $result;
	}

}

?>
