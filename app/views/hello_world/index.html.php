<?php
//$transactions = json_decode($transactions);

print_r($transactions['confirmations']);
//exit;
echo '<hr />';
echo '<p>Confirmations:</p> ';
//print_r($transactions->outputs );

foreach($transactions as $key => $value) {

echo '<p>';
	echo $key .': '; print_r($value);
echo '</p>';
	} 

?>
