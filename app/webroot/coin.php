<?php

 function create_address($username,$password) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/account/createaddress");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

	curl_setopt($ch, CURLOPT_POSTFIELDS, "{
	  \"alias\": \"label\"
	}");

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
 	 "Content-Type: application/json",
    "X-Coinprism-Username: $username",
  	 "X-Coinprism-Password: $password"
	));
	print_r($ch);
	$response = curl_exec($ch);
	
	curl_close($ch);
	var_dump($response);
	if (!$response) {
  return array('error'=>'Unable to connect to Coinprism API.');
 }

	$response = json_decode($response,true);


	//change the terminology to how we use it elsewhere to be consistant. And lose the private key!
	$response = array('btc_address' => $response['bitcoin_address'], 'cc_address' => $response['asset_address']);
//print_r($response);
	return $response;

	}
?>
