<?php	

use lithium\util\String;
use app\extensions\action\Money;

$sel_curr = $this->_request->params['args'][0];
$first_curr = strtoupper(substr($sel_curr,0,3));
$second_curr = strtoupper(substr($sel_curr,4,3));
$BalanceFirst = $details['balance'][$first_curr];
$$first_curr = $details['balance'][$first_curr];
$BalanceSecond = $details['balance'][$second_curr];
$$second_curr = $details['balance'][$second_curr];
if (is_null($BalanceFirst)){$BalanceFirst = 0;}
if (is_null($BalanceSecond)){$BalanceSecond = 0;}

$btc_tcp = array();
$btc_dct = array();
$tcp_dct = array();

//TODO: Is there a more efficient way of doing this?
foreach($YourOrders as $foo) {

//format the date from now on
$foo['DateTime'] = gmdate('d-M-Y H:i:s',$foo['DateTime']->sec);

$money = new Money($user_id);
//format money
$foo['Amount'] = $money->display_money($foo['Amount'], $foo['FirstCurrency']);
$foo['Price'] = $money->display_money($foo['Price'], $foo['SecondCurrency']);


	if( ('BTC' == $foo['FirstCurrency']) && ('TCP' == $foo['SecondCurrency']) ) { 

		$btc_tcp[] = array('id' => $foo['_id'], 'date' => $foo['DateTime'], 'type' => $foo['Type'], 'amount' => $foo['Amount'], 'price' => $foo['Price']); 
	}
	
	elseif( ('BTC' == $foo['FirstCurrency']) && ('DCT' == $foo['SecondCurrency']) ) { 

		$btc_dct[] = array('id' => $foo['_id'], 'date' => $foo['DateTime'], 'type' => $foo['Type'], 'amount' => $foo['Amount'], 'price' => $foo['Price']); 
	}
	if( ('TCP' == $foo['FirstCurrency']) && ('DCT' == $foo['SecondCurrency']) ) { 

		$tcp_dct[] = array('id' => $foo['_id'], 'date' => $foo['DateTime'], 'type' => $foo['Type'], 'amount' => $foo['Amount'], 'price' => $foo['Price']); 
	}
}
reset($YourOrders);
?>
<h2>Open Orders</h2>

                <div class="col-sm-10 col-lg-6">
                        <div class="panel panel-info">
                                <div class="panel-heading">
<ul class="nav nav-tabs">
  <li class="active"><a aria-expanded="true" href="#all" data-toggle="tab">All Markets</a></li>
  <li class=""><a aria-expanded="false" href="#btc_tcp" data-toggle="tab">BTC / TCP</a></li>
  <li class=""><a aria-expanded="false" href="#btc_dct" data-toggle="tab">BTC / DCT</a></li>
  <li class=""><a aria-expanded="false" href="#tcp_dct" data-toggle="tab">TCP / DCT</a></li>
</ul>
                                </div>
                      
<!--	<div id="YourOrders" style="overflow:auto;" class="fade in">-->

<div id="myTabContent" class="tab-content">

  <div class="tab-pane fade active in" id="all">

		<?php if(0 == count($YourOrders) ) { ?>

						<p style="padding:15px;">You do not have any open orders.</p>

		<?php } else { ?>

                        <table class="table table-condensed table-bordered table-hover" style="font-size:11px">
                                <thead>
                                        <tr>
                                                <th style="text-align:left ">Market</th>
                                                <th style="text-align:center ">Type</th>
                                                <th style="text-align:right ">Quantity</th>
                                                <th style="text-align:right ">Price</th>
                                                <th style="text-align:right ">&nbsp;</th>
                                        </tr>
                                </thead>
                                <tbody>
	
	
                                <?php foreach($YourOrders as $YO){ ?>

                                        <tr>
                                                <td style="text-align:left "><?=$YO['FirstCurrency']?> / <?=$YO['SecondCurrency']?></td>
                                        	<td style="text-align:center"><?=ucfirst($YO['Type'])?></td>
						<td style="text-align:right "><?=$YO['Amount']?></td>
						<td style="text-align:right "><?=$YO['Price']?></td>
                                       		<td style="text-align:center"><a href="/new_trade/RemoveOrder/<?=String::hash($YO['_id'])?>/<?=$YO['_id']?>/<?=$sel_curr?>" title="Remove this order"><i class="fa fa-times"></i></a></td>
					 </tr>
                                <?php } ?>
                                </tbody>
                        </table>

		<?php }?>
 </div>

  <div class="tab-pane fade" id="btc_tcp">

                        <table class="table table-condensed table-bordered table-hover" style="font-size:11px">
                                <thead>
                                        <tr>
                                                <th style="text-align:left ">Date &amp; Time</th>
                                                <th style="text-align:center ">Type</th>
                                                <th style="text-align:right ">Quantity</th>
                                                <th style="text-align:right ">Price</th>
                                                <th style="text-align:right ">&nbsp;</th>
                                        </tr>
                                </thead>
                                <tbody>
	
		<?php if(0 == count($btc_tcp) ) { ?>

					<tr><td colspan=5><p style="padding:15px;">You do not have any open orders in this market.</p></td></tr>

		<?php } else { ?>
	
                                <?php foreach($btc_tcp as $YO){ ?>

                                        <tr>
                                                <td style="text-align:left "><?=$YO['date']?></td>
                                        	<td style="text-align:center"><?=ucfirst($YO['type'])?></td>
						<td style="text-align:right "><?=$YO['amount']?></td>
						<td style="text-align:right "><?=$YO['price']?></td>
                                       		<td style="text-align:center"><a href="/new_trade/RemoveOrder/<?=String::hash($YO['id'])?>/<?=$YO['id']?>/<?=$sel_curr?>" title="Remove this order"><i class="fa fa-times"></i></a></td>
					 </tr>
                                <?php }
					}?>
                                </tbody>
                        </table>


  </div>

  <div class="tab-pane fade" id="btc_dct">

                        <table class="table table-condensed table-bordered table-hover" style="font-size:11px">
                                <thead>
                                        <tr>
                                                <th style="text-align:left ">Date &amp; Time</th>
                                                <th style="text-align:center ">Type</th>
                                                <th style="text-align:right ">Quantity</th>
                                                <th style="text-align:right ">Price</th>
                                                <th style="text-align:right ">&nbsp;</th>
                                        </tr>
                                </thead>
                                <tbody>
	
		<?php if(0 == count($btc_dct) ) { ?>

					<tr><td colspan=5><p style="padding:15px;">You do not have any open orders in this market.</p></td></tr>

		<?php } else { ?>
	
                                <?php foreach($btc_dct as $YO){ ?>

                                        <tr>
                                                <td style="text-align:left "><?=$YO['date']?></td>
                                        	<td style="text-align:center"><?=ucfirst($YO['type'])?></td>
						<td style="text-align:right "><?=$YO['amount']?></td>
						<td style="text-align:right "><?=$YO['price']?></td>
                                       		<td style="text-align:center"><a href="/ex/RemoveOrder/<?=String::hash($YO['id'])?>/<?=$YO['id']?>/<?=$sel_curr?>" title="Remove this order"><i class="fa fa-times"></i></a></td>
					 </tr>
                                <?php }
					}?>
                                </tbody>
                        </table>

  </div>

  <div class="tab-pane fade" id="tcp_dct">
  
                        <table class="table table-condensed table-bordered table-hover" style="font-size:11px">
                                <thead>
                                        <tr>
                                                <th style="text-align:left ">Date &amp; Time</th>
                                                <th style="text-align:center ">Type</th>
                                                <th style="text-align:right ">Quantity</th>
                                                <th style="text-align:right ">Price</th>
                                                <th style="text-align:right ">&nbsp;</th>
                                        </tr>
                                </thead>
                                <tbody>
	
		<?php if(0 == count($tcp_dct) ) { ?>

					<tr><td colspan=5><p style="padding:15px;">You do not have any open orders in this market.</p></td></tr>

		<?php } else { ?>
	
                                <?php foreach($tcp_dct as $YO){ ?>

                                        <tr>
                                                <td style="text-align:left "><?=$YO['date']?></td>
                                        	<td style="text-align:center"><?=ucfirst($YO['type'])?></td>
						<td style="text-align:right "><?=$YO['amount']?></td>
						<td style="text-align:right "><?=$YO['price']?></td>
                                       		<td style="text-align:center"><a href="/ex/RemoveOrder/<?=String::hash($YO['id'])?>/<?=$YO['id']?>/<?=$sel_curr?>" title="Remove this order"><i class="fa fa-times"></i></a></td>
					 </tr>
                                <?php }
					}?>
                                </tbody>
                        </table>

  </div>
</div>
</div>

