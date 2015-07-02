<?php
namespace app\extensions\action;
use lithium\action\DispatchException;
use app\extensions\action\Monitor;
use app\models\Addresses;
use \CoinAddress;

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
	We'll generate a new btc address and key locally, then use coinprism to get the colored details of the address
*
*/
	public function create_address($user_id, $label = null) {


	CoinAddress::set_debug(false);
        CoinAddress::set_reuse_keys(false);
	
	$coin = CoinAddress::bitcoin();	

	$btc_address = $coin['public_compressed']; //base58check
        $private_key = $coin['private_hex']; //hex
	//$wif_key     = $coin['private_compressed']; //WIF
	

	$cp = self::get_address($btc_address);

	$cc_address = $cp['asset_address'];

	//tell chain.com to monitor the address	
	$monitor = new Monitor(CHAIN_API_KEY, CHAIN_SECRET, CHAIN_CALLBACK_URL);
	if(! $foo = $monitor->monitor_address($btc_address) ) {

		return array('error' => 'Unable to register address for monitoring. Not saved!');
	}

	if(! $this->save_address($user_id, $label, $btc_address, $cc_address, $private_key) ) {
			
			return array('error'=>'Unable to save new address!');
		}

	return array('btc_address' => $btc_address, 'asset_address' => $cc_address);

	}//done




	public static function validate_address($address, $currency = false) {

	$data = self::get_address($address);

	if('' == $data['btc_address']['address']) return false; 

	if(! $currency) return $data;

	if( ('BTC' == $currency) && ($address != $data['btc_address']['address']) ) return false;
	if( ('BTC' != $currency) && ($address != $data['asset_address']) ) return false;

	return $data;
	}


/*
	We can use get_address to check the address is valid, or to retrieve the corresponding btc / asset address and balances
	
	@param $address (string) either a btc address or an asset address
	@return (mixed) an array containing information about the address if valid, else false
*/
	public static function get_address($address) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/addresses/$address");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	$response = json_decode(curl_exec($ch));
	curl_close($ch);


	if(isset($response->ErrorCode)) { return false; }

	else { return array('btc_address' => array('address' => $response->bitcoin_address, 'balance' => $response->balance), 'asset_address' => $response->asset_address); }
	}


	/*
	 @param $flag (string) Set to ALL if you want the whole response returned without filtering. I.e. don't limit to deposit addresses.
	*/
	public function get_transaction($txhash, $flag = false) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/transactions/$txhash");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	$response = curl_exec($ch);
	curl_close($ch);

	if (!$response) {
                        return array('error'=>'Unable to connect to Coinprism API.');
                }

	$response = json_decode($response);

		//return response without further processing
		if('ALL' == $flag) {
			 return $response;

		}

	$results = array('hash' => $txhash, 'confirmations' => $response->confirmations); 

		foreach($response->outputs as $output) {
	
	//find out which user owns the address and get the cc_address
	if(! $search = self::search_address($output->addresses[0]) ) continue;

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

	private function save_address($user_id, $label, $btc_address, $cc_address, $private_key, $wif_key = '') {


	$data = Addresses::create(array('user_id' => $user_id,
					'label'   => $label,
					'btc_address' => $btc_address,
					'cc_address'  => $cc_address,
					'private_key' => $private_key,
					//'wif_key' => $wif_key,
					));
	$result = $data->save();
	
	return $result; 
	}

	public static function search_address($btc_address) {


	$search = Addresses::find('first',
			array('conditions'=>array('btc_address'=>$btc_address))
		);

	if( count($search)!=1) return false;

	else return $search;

	}


	public static function send($input, $outputs) {

	$privkey = $input['key'];
	$input = $input['address'];

	if(('' == $input) 
	OR ('' == $privkey)
	OR (0 == count($outputs))) {

	return false;
	}

		//is it an asset transaction?
		if(isset($outputs[0]['asset_id'])) {

		$raw = self::send_asset($input, $outputs);
		}
		
		else { //must be bitcoin

		$raw = self::send_bitcoin($input, $outputs);
	
		}

		if('' == $raw) {
				return false;
				}

			//sign the transaction
			$signed = self::sign($raw, $privkey);

			if('' == $signed) {
					 return false;
					}
			//broadcast
			$tx_hash = self::broadcast($signed);

			if('' == $tx_hash) {
						return false;
					}
	return $tx_hash;
	}

	/*
	Broadcast a signed transaction to the network and return its hash if successful
	@param $transaction (string) the transaction in hex format
	*/
	public static function broadcast($transaction) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/sendrawtransaction");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

	curl_setopt($ch, CURLOPT_POSTFIELDS, "\"$transaction\"");

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
 	 "Content-Type: application/json"
	));

	$response = json_decode(curl_exec($ch));
	curl_close($ch);

	return $response;
	}

	/*
	Signs an unsigned transaction given its raw hex representation and the private keys for addresses in input. Keys must be sent in hex form.
	@param $transaction (string) the raw hex representation of the transaction. Probably the result of send_bitcoin() or send_asset();
	@param $keys (string) the private key in hex format of the transaction input 
	*/
	public static function sign($transaction, $keys) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/signtransaction");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

	$privkeys = "\"$keys\"";

$json = "{
 	 \"transaction\": \"$transaction\",
  	\"keys\": [
    		$privkeys
  		]
	}";

	curl_setopt($ch, CURLOPT_POSTFIELDS, $json); 

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  	"Content-Type: application/json"
	));

	$response = json_decode(curl_exec($ch));
	curl_close($ch);
	
	return $response->raw;
	}


	/*
	Send asset to one or more address 

	@param $input (string) the sending BITCOIN address - coinprism API only allows one input address
	@param $outputs (array) the recipient ASSET addresses in array format containing output asset_id, address and amount
	@return (mixed) false if error, else the raw hex unsigned transaction, ready to be passed to sign()
	*/
	public static function send_asset($input, $outputs) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/sendasset?format=raw");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

		$from = "\"$input\"";

		$to = '';
		foreach($outputs as $output) {
		$to .= "{\"address\": \"{$output['address']}\",
			 \"amount\": \"{$output['amount']}\",
			 \"asset_id\": \"{$output['asset_id']}\"
		   	}, ";
		
		}
	$to = substr($to, 0, -2);

	$fees = DEFAULT_TRANSACTION_FEE;

	curl_setopt($ch, CURLOPT_POSTFIELDS, "{
	  \"fees\": $fees,
  	  \"from\": $from,
  	  \"to\": [
  		  $to
		]
	}");

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  		"Content-Type: application/json"
	));

	$response = json_decode(curl_exec($ch));
	curl_close($ch);

	return $response->raw;
	}


	/*
	Send bitcoins to one or more address 

	@param $input (string) the sending address - coinprism API only allows one input address
	@param $outputs (array) the recipient addresses in array format containing output address and the amount
	@return (mixed) false if error, else the raw hex unsigned transaction
	*/
	public static function send_bitcoin($input, $outputs) {

	$money = new Money();

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.coinprism.com/v1/sendbitcoin?format=raw");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_POST, TRUE);

		$from = "\"$input\"";
		
		$to = '';
		foreach($outputs as $output) {
		$amount = $output['amount'];
		$to .= "{\"address\": \"{$output['address']}\", \"amount\": \"$amount\"}, ";
		}
		
	$to = substr($to, 0, -2);
	$fees = DEFAULT_TRANSACTION_FEE;

	$json =  "{
	 \"fees\": $fees,
  	 \"from\": $from,
  	 \"to\": [
  		 $to
		]
	}";

	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
 	 "Content-Type: application/json"
	));

	$response = json_decode(curl_exec($ch));
	curl_close($ch);

	return $response->raw;
	}

}
