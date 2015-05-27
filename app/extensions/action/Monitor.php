<?php
namespace app\extensions\action;
use lithium\action\DispatchException;

class Monitor extends \lithium\action\Controller{
// @var string
        private $api_key;
        // @var string
        private $api_secret;
	// @var string
	private $callback_url;

/**
* Contructor
*
* @param string $username
* @param string $password
*/
        public function __construct($api_key, $api_secret, $callback_url) {
        //connection details
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
	$this->callback_url = $callback_url;
        }

	
	public function monitor_address($address) {

	$url = "https://".$this->api_key.":".$this->api_secret."@api.chain.com/v2/notifications";

	$ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

	curl_setopt($ch, CURLOPT_POSTFIELDS, "{
	  \"type\": \"address\",
	  \"block_chain\": \"bitcoin\", 
	  \"address\": \"$address\", 
	  \"url\": \"$this->callback_url\"
	}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         "Content-Type: application/json",
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
                        return array('error'=>'Unable to connect to chain.com.');
                }
	
	return $response;
	}
}
?>
