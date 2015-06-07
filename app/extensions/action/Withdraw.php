<?php
namespace app\extensions\action;
use lithium\action\DispatchException;
use app\extensions\action\Monitor;
use app\models\Addresses;

class Withdraw extends \lithium\action\Controller{
// @var string
        private $coinprism_username;
        // @var string
        private $coinprism_password;

/**
* Contructor
*
* @param string $username
* @param string $password
*/
        public function __construct($username, $password) {
        //connection details
        $this->coinprism_username = $username;
        $this->coinprism_password = $password;
        }


	/*
	@param array $inputs - an array of all the input addresses
	@param array $outputs - a multidimensional array including the recipient address, amount and asset ID
	@return the response from coinprism 
	*/
	public function send_asset($inputs, $outputs) {

	$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/sendasset?format=json
");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_POST, TRUE);

curl_setopt($ch, CURLOPT_POSTFIELDS, "{
  \"fees\": 1000,
  \"from\": \"1zLkEoZF7Zdoso57h9si5fKxrKopnGSDn\",
  \"to\": [
    {
      \"address\": \"akSjSW57xhGp86K6JFXXroACfRCw7SPv637\",
      \"amount\": \"10\",
      \"asset_id\": \"AHthB6AQHaSS9VffkfMqTKTxVV43Dgst36\"
    }
  ]
}");

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json"
));

$response = curl_exec($ch);
curl_close($ch);

var_dump($response);

die;

/*
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/sendasset?format=json");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

		foreach($inputs as $input) {

		$from .= "\"$input\",";
		}

		foreach($outputs as $output) {

		$to .= "
			{
			\"address\": \"{$output['address']}\",
			\"amount\": \"{$output['amount']}\",
			\"asset_id\": \"{$output['asset_id']}\"
			}
			";
		}

	curl_setopt($ch, CURLOPT_POSTFIELDS, '{

	 \"fees\": 1000,

	  \"from\": [
		'.     $from .'
		    ]

	  \"to\": [
 	 	 '.  $to .'
		  ]
}');
//);
//return $data;


	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
 	 "Content-Type: application/json"
	));

	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
*/
	}

}
