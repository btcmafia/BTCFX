<?php

$hex = strToHex("http://ibwt.co.uk/encrypt");

$opts = array(
  'http'=> array(
	'method'=> "GET",
	'user_agent'=> "MozillaXYZ/1.0"));
	$context = stream_context_create($opts);
	$json = file_get_contents('https://blockchain.info/q/hashtoaddress/'.$hex, false, $context);

print_r($json);

function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}
function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}
?>