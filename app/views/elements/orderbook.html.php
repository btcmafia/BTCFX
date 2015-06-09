<?php
if($type == 'bids') { $orders = $orders['bids']; }
elseif($type == 'asks') { $orders = $orders['asks']; }

?>

<table class="table table-condensed table-bordered table-hover"  style="font-size:12px ">
                                <thead>
                                        <tr>
                                        <th style="text-align:center ">Price (<?=$second_curr?>)</th>
                                        <th style="text-align:center ">Quantity (<?=$first_curr?>)</th>
                                        <th style="text-align:center ">Value (<?=$second_curr?>)</th>
                                        </tr>
                                </thead>
                                <tbody>
<?php

	if(0 == count($orders)) {

		echo "<tr><td colspan=3 style='text-align:center'>No $type found</td></tr>";

	}
	else {

	foreach($orders as $order) {

$price = $order[0];
$amount = $order[1];
$value = $price * $amount;
?>
			<tr style="text-align:center;"><td><?=$price?></td><td><?=$amount?></td><td><?=$value?></td></tr>                                        
<?php
        }
	}
?>
                                </tbody>
                        </table>

