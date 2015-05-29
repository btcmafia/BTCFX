<?php
namespace app\extensions\action;
use lithium\action\DispatchException;
use app\extensions\action\Monitor;
use app\models\Addresses;

class Coinprism extends \lithium\action\Controller{
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

/**
*Gets a new address from Coinprism, monitors it and saves it
*
*/
	public function create_address($user_id, $label = null) {

	$label = "New Address AB";

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/account/createaddress");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

	curl_setopt($ch, CURLOPT_POSTFIELDS, "{
	  \"alias\": \"$label\"
	}");

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
 	 "Content-Type: application/json",
         "X-Coinprism-Username: $this->coinprism_username",
  	 "X-Coinprism-Password: $this->coinprism_password"
	));

	$response = curl_exec($ch);
	curl_close($ch);

	if (!$response['bitcoin_address']) {
                        return array('error'=>'Unable to connect to Coinprism API.');
                }

	$response = json_decode($response,true);
/*
echo "<p>Username: " . $this->coinprism_username;
echo '<p>Password: ' . $this->coinprism_password;

echo "<p>Response: ";
print_r($response);
echo '</p>';
die;
*/	
//if($response['bitcoin_address'] == '') {
//mail('stephen@joopla.co.uk', 'No address from Coinprism!!', "Response:\n\n" . var_dump($response, true) . "\n\nUsername: " . $this->coinprism_username . "\n\nPassword: " . $this->coinprism_password);
//}

	$monitor = new Monitor(CHAIN_API_KEY, CHAIN_SECRET, CHAIN_CALLBACK_URL);
	if(! $foo = $monitor->monitor_address($response['bitcoin_address']) ) {

		return array('error' => 'Unable to register address for monitoring. Not saved!');
	}

	if(! $this->save_address($user_id, $label, $response['bitcoin_address'], $response['asset_address'], $response['private_key']) ) {
			
			return array('error'=>'Unable to save new address!');
		}

	//change the terminology to how we use it elsewhere to be consistant. And lose the private key!
	$response = array('btc_address' => $response['bitcoin_address'], 'cc_address' => $response['asset_address']);

	return $response;

	}

	public function get_transaction($txhash) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/transactions/$txhash");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	$response = curl_exec($ch);
	curl_close($ch);

	if (!$response) {
                        return array('error'=>'Unable to connect to Coinprism API.');
                }

	//return $response;

	$response = json_decode($response);

	$results = array('hash' => $txhash, 'confirmations' => $response->confirmations); 

		foreach($response->outputs as $output) {
	
	//find out who which user owns the address and get the cc_address
	if(! $search = $this->search_address($output->addresses[0]) ) continue;

			if($output->asset_id == DCT_ASSET_ID) {

			$currency = 'DCT';
			$amount   = $output->asset_quantity; //in cents
			$address  = $search['cc_address'];
			}
			elseif($output->asset_id == TCP_ASSET_ID) {

			$currency = 'TCP';
			$amount   = $output->asset_quantity; //in pence
			$address  = $search['cc_address'];

			}
			elseif($output->asset_id == '') {

			$currency = 'BTC';
			$amount   = $output->value; //in satoshis
			$address  = $search['btc_address'];

			}

		$results['deposits'][] = array('user_id' => $search['user_id'], 'currency' => $currency, 'address' => $address, 'amount' => $amount);	
		
		}
		
		return $results;
	}

	private function save_address($user_id, $label, $btc_address, $cc_address, $private_key) {


	$data = Addresses::create(array('user_id' => $user_id,
					'label'   => $label,
					'btc_address' => $btc_address,
					'cc_address'  => $cc_address,
					'private_key' => $private_key));
	$result = $data->save();
	
	return $result; 
	}

	private function search_address($btc_address) {


	$search = Addresses::find('first',
			array('conditions'=>array('btc_address'=>$btc_address))
		);

	if( count($search)!=1) return false;



	//echo "Returning for: $btc_address<br />";

	else return $search;

	}

}
