<?php
use lithium\util\String;
use app\models\Trades;
$virtuals = array('BTC');
$virtualcurrencies = Trades::find('all',array(
	'conditions'=>array('SecondType'=>'Virtual')
));
foreach($virtualcurrencies as $VC){
	array_push($virtuals,substr($VC['trade'],4,3));
}
?>
<div class="panel panel-default">
 
    <h3 class="titleHeader">Dashboard: <?=$user['firstname']?> <?=$user['lastname']?></h3>
	    <a href="/users/transactions"  style="padding-right:10px;" class="floatRight">Transactions</a>
		<a href="/users/settings" class="floatRight"  style="padding-right:10px;" >Settings</a>
		<a href="/print/" class="floatRight">Print / Cold Storage</a>
  <div class="panel-body">
		<div class="row">
		<!--Options-->
			<div class="col-md-12">
				<div class="panel panel-success">
					
						<h3 class="globalHead">Your status</h3>
					
					<div class="panel-body">
					<table class="table">
						<tr>
							<td width="20%">
<!-- Email start-->					
					<?php 
					if($details['email.verified']=='Yes'){
					?><a href="#" class="btn btn-success   btn-sm btn-block" rel="tooltip-x" data-placement="top" title="Completed!"><i class="glyphicon glyphicon-ok "></i> Email</a><?php }else{
					?><a href="/users/email/" class="btn btn-warning   btn-sm btn-block " rel="tooltip-x" data-placement="top" title="Compulsary to transact!"><i class="glyphicon glyphicon-remove"></i> Email</a><?php }
					?>
						</td>
<!-- Email end-->										
						<?php 
						$alldocuments = array();
						$i=0;		
						foreach($settings['documents'] as $documents){
							if($documents['required']==true){
									if($documents['alias']==""){
										$name = $documents['name'];
									}else{
										$name = $documents['alias'];
									}
								if(strlen($details[$documents['id'].'.verified'])==0){
										$alldocuments[$documents['id']]="No";
						?>
					<td width="20%"><a href="/users/settings/<?=$documents['id']?>" class="btn   btn-sm btn-block btn-warning" rel="tooltip-x" data-placement="top" title="Compulsary to transact!"><i class="glyphicon glyphicon-remove"></i> <?=$name?></a></td>
					<?php }elseif($details[$documents['id'].'.verified']=='No'){
							$alldocuments[$documents['id']]="Pending";
					?>
	<td width="20%"><a href="#" class="btn btn-danger   btn-sm btn-block -x" rel="tooltip-x" data-placement="top" title="Pending verification!"><i class="glyphicon glyphicon-edit"></i> <?=$name?></a></td>
					<?php }else{
						$alldocuments[$documents['id']]="Yes";
					?>
					<td width="20%"><a href="#" class="btn btn-success   btn-sm btn-block" rel="tooltip-x" data-placement="top" title="Completed!"><i class="glyphicon-ok glyphicon"></i> <?=$name?></a></td>
	<?php }
			}
			$i++;
		}
?>
<!-- Mobile start-->			
				<td width="20%">
					<?php 
					if($details['mobile.verified']=='Yes'){
					?><a href="#" class="btn btn-success   btn-sm btn-block " rel="tooltip-x" data-placement="top" title="Completed!"><i class="glyphicon glyphicon-ok"></i> Mobile/Phone</a><?php }else{
					?><a href="/users/mobile/" class="btn  btn-sm btn-block btn-warning " rel="tooltip-x" data-placement="top" title="Optional!"><i class="glyphicon glyphicon-remove"></i> Mobile/Phone</a><?php }
					?>
					</td>
<!-- Mobile end-->															
		</tr>
		<tr>
<?php	

		$all = true;
/*			foreach($alldocuments as $key=>$val){						
			if($val!='Yes'){
			$all = false;
			break;
			}
		}
*/

		?>
		<td colspan="5"><h3 class="globalHead">Fund Your Account</h3>
		<table class="table"><tr>
<?php 
$trades = Trades::find('all');
$currencies = array();
$VirtualCurr = array(); $FiatCurr = array();
foreach($trades as $tr){
	$first_curr = substr($tr['trade'],0,3);
	array_push($currencies,$first_curr);
	$second_curr = substr($tr['trade'],4,3);
	array_push($currencies,$second_curr);

		if($tr['FirstType']=='Virtual'){
			array_push($VirtualCurr,$first_curr);
			}else{
			array_push($VirtualCurr,$first_curr);
		}
		if($tr['SecondType']=='Virtual'){
			array_push($VirtualCurr,$second_curr);
			}else{
			array_push($FiatCurr,$second_curr);
		}
	
	
	
}	//for
	$currencies = array_unique($currencies);
	$VirtualCurr = array_unique($VirtualCurr);
	$FiatCurr = array_unique($FiatCurr);
	foreach($VirtualCurr as $currency){
		echo '<td><a href="/users/funding/'.$currency.'" class="btn btn-primary btn-sm btn-block"> '.$currency.' </a></td>';
	}
	if($all){
		foreach($FiatCurr as $currency){
			echo '<td><a href="/users/funding_fiat/'.$currency.'" class="btn btn-primary btn-sm btn-block"> '.$currency.' </a></td>';
		}
	} //if all
?>
</tr></table>
</td>
</tr>
<tr>
		
	</tr>

		</table>
					</div>
				</div>
			</div>
		<!-- Options -->
		<!--Summary-->
			<div class="col-md-12">
				<div class="panel panel-success">
				
					<h3 class="globalHead">Summary of accounts</h3>
					
					<div class="panel-body">
		<table class="table table-condensed table-bordered table-hover">
			<thead>
				<tr>
					<th  class="headTable">Currency</th>
					<?php 
					$currencies = array();
					$trades = Trades::find('all');					
					foreach($trades as $tr){
						$currency = substr($tr['trade'],0,3);
						array_push($currencies,$currency);
						$currency = substr($tr['trade'],4,3);
						array_push($currencies,$currency);
					 }	//for
					$currencies = array_unique($currencies);
					foreach($currencies as $currency){?>
					<th class="headTable" style="text-align:center"><?=$currency?></th>
					<?php }?>
				</tr>
			</thead>
<?php 
if(count($YourOrders['Buy']['result'])>0){
	foreach($YourOrders['Buy']['result'] as $YO){
		$Buy[$YO['_id']['FirstCurrency']] = $Buy[$YO['_id']['FirstCurrency']] + $YO['Amount'];
		$BuyWith[$YO['_id']['SecondCurrency']] = $BuyWith[$YO['_id']['SecondCurrency']] + $YO['TotalAmount'];					
	}
}
if(count($YourOrders['Sell']['result'])>0){
	foreach($YourOrders['Sell']['result'] as $YO){
		$Sell[$YO['_id']['FirstCurrency']] = $Sell[$YO['_id']['FirstCurrency']] + $YO['Amount'];
		$SellWith[$YO['_id']['SecondCurrency']] = $SellWith[$YO['_id']['SecondCurrency']] + $YO['TotalAmount'];					
	}
}
if(count($YourCompleteOrders['Buy']['result'])>0){
	foreach($YourCompleteOrders['Buy']['result'] as $YCO){
		$ComBuy[$YCO['_id']['FirstCurrency']] = $ComBuy[$YCO['_id']['FirstCurrency']] + $YCO['Amount'];
		$ComBuyWith[$YCO['_id']['SecondCurrency']] = $ComBuyWith[$YCO['_id']['SecondCurrency']] + $YCO['TotalAmount'];					
	}
}
if(count($YourCompleteOrders['Sell']['result'])>0){
	foreach($YourCompleteOrders['Sell']['result'] as $YCO){
		$ComSell[$YCO['_id']['FirstCurrency']] = $ComSell[$YCO['_id']['FirstCurrency']] + $YCO['Amount'];
		$ComSellWith[$YCO['_id']['SecondCurrency']] = $ComSellWith[$YCO['_id']['SecondCurrency']] + $YCO['TotalAmount'];					
	}
}
?>			
<?php
foreach($Commissions['result'] as $C){
	foreach($currencies as $currency){
		if($C['_id']['CommissionCurrency']==$currency){
			$variablename = $currency."Comm";
			$$variablename = $C['Commission'];		
		}
	}
}
foreach($CompletedCommissions['result'] as $C){
	foreach($currencies as $currency){
		if($C['_id']['CommissionCurrency']==$currency){
			$variablename = "Completed".$currency."Comm";
			$$variablename = $C['Commission'];		
		}
	}
}
?>
			<tbody>
				<tr>
					<td class="rightTable"><strong>Opening Balance</strong></td>
					<?php foreach($currencies as $currency){
							if(in_array($currency,$virtuals)){
					?>
					<td style="text-align:right"><?=number_format($details['balance.'.$currency]+$Sell[$currency],8)?></td>					
					<?php }else{?>
					<td style="text-align:right"><?=number_format($details['balance.'.$currency]+$Sell[$currency],4)?></td>										
					<?php }}?>					
				</tr>
				<tr>
					<td class="rightTable"><strong>Current Balance</strong><br>
					(including pending orders)</td>
					<?php foreach($currencies as $currency){
						if(in_array($currency,$virtuals)){
					?>
						<td style="text-align:right "><?=number_format($details['balance.'.$currency],8)?></td>
					<?php }else{?>
						<td style="text-align:right "><?=number_format($details['balance.'.$currency],4)?></td>					
					<?php }}?>					
				</tr>
				<tr>
					<td class="rightTable"><strong>Pending Buy Orders</strong></td>
					<?php foreach($currencies as $currency){
						if(in_array($currency,$virtuals)){
						?>
					<td style="text-align:right ">+<?=number_format($Buy[$currency],8)?></td>
					<?php }else{?>
					<td style="text-align:right ">-<?=number_format($BuyWith[$currency],4)?></td>										
					<?php }
					}?>					
				</tr>
				<tr>
					<td class="rightTable"> <strong>Pending Sell Orders</strong></td>
					<?php foreach($currencies as $currency){
						if(in_array($currency,$virtuals)){
						?>
					<td style="text-align:right ">-<?=number_format($Sell[$currency],8)?></td>
					<?php }else{?>
					<td style="text-align:right ">+<?=number_format($SellWith[$currency],4)?></td>										
					<?php }
					}?>					
				</tr>
				<tr>
					<td class="rightTable"><strong>After Execution</strong></td>
					<?php foreach($currencies as $currency){
						if(in_array($currency,$virtuals)){
						$variablename = $currency."Comm";
						?>
					<td style="text-align:right "><?=number_format($details['balance.'.$currency]+$Buy[$currency]-$$variablename,8)?></td>
					<?php }else{?>
					<td style="text-align:right "><?=number_format($details['balance.'.$currency]+$SellWith[$currency]-$$variablename,4)?></td>					
					<?php }
					}?>					
				</tr>
				<tr >
					<td class="rightTable"><strong>Commissions</strong></td>
					<?php foreach($currencies as $currency){
					
						$variablename = $currency."Comm";
						if(in_array($currency,$virtuals)){
						?>
					<td style="text-align:right "><?=number_format($$variablename,8)?></td>
					<?php }else{?>
					<td style="text-align:right "><?=number_format($$variablename,4)?></td>					
					<?php }}?>					
				</tr>
				<tr>
					<td class="rightTable"><strong>Complete Buy Orders</strong></td>
					<?php foreach($currencies as $currency){
						if(in_array($currency,$virtuals)){
						?>
					<td style="text-align:right "><?=number_format($ComBuy[$currency],8)?></td>
					<?php }else{?>
					<td style="text-align:right "><?=number_format($ComBuyWith[$currency],4)?></td>										
					<?php }
					}?>					
				</tr>
				<tr>
					<td class="rightTable"><strong>Complete Sell Orders</strong></td>
					<?php foreach($currencies as $currency){
						if(in_array($currency,$virtuals)){
						?>
					<td style="text-align:right "><?=number_format($ComSell[$currency],8)?></td>
					<?php }else{?>
					<td style="text-align:right "><?=number_format($ComSellWith[$currency],4)?></td>										
					<?php }
					}?>					
				</tr>
				<tr>
					<td class="rightTable"><strong>Completed Order Commissions</strong></td>
					<?php foreach($currencies as $currency){
							$variablename = "Completed".$currency."Comm";
							if(in_array($currency,$virtuals)){
						?>
					<td style="text-align:right "><?=number_format($$variablename,8)?></td>
					<?php }else{?>					
					<td style="text-align:right "><?=number_format($$variablename,4)?></td>					
					<?php }}?>										
				</tr>
			</tbody>
		</table>
					</div>
				</div>
			</div>		
		<!--Summary-->
		<!-- final summary-->
			<div class="col-md-12">
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">Users: <?=$UsersRegistered?> / Online: <?=$OnlineUsers?></h3>
					</div>
					<div class="panel-body">
		<table class="table table-condensed table-bordered table-hover">
				<tr>
					<th>Status</th>
					<th>BTC</th>
					<th>Amount</th>					
					<th>Avg Price</th>										
				</tr>
				<tr>
					<th colspan="4">Pending orders</th>
				</tr>
				<?php 
				if(count($TotalOrders['Buy']['result'])>0){
					foreach ($TotalOrders['Buy']['result'] as $r){ ?>
					<tr>
						<td><?=$r['_id']['Action']?> <?=$r['_id']['FirstCurrency']?> with <?=$r['_id']['SecondCurrency']?></td>
						<td style="text-align:right "><?=number_format($r['Amount'],8)?></td>
						<td style="text-align:right "><?=number_format($r['TotalAmount'],8)?></td>						
						<td style="text-align:right "><?=number_format($r['TotalAmount']/$r['Amount'],8)?></td>												
					</tr>
				<?php }
				}?>
				<?php 
				if(count($TotalOrders['Sell']['result'])>0){
					foreach ($TotalOrders['Sell']['result'] as $r){ ?>
					<tr>
						<td><?=$r['_id']['Action']?> <?=$r['_id']['FirstCurrency']?> with <?=$r['_id']['SecondCurrency']?></td>
						<td style="text-align:right "><?=number_format($r['Amount'],8)?></td>
						<td style="text-align:right "><?=number_format($r['TotalAmount'],8)?></td>						
						<td style="text-align:right "><?=number_format($r['TotalAmount']/$r['Amount'],8)?></td>																		
					</tr>
				<?php }
				}?>
				<tr>
					<th colspan="4">Completed orders</th>
				</tr>
				<?php 
				if(count($TotalCompleteOrders['Buy']['result'])>0){
					foreach ($TotalCompleteOrders['Buy']['result'] as $r){ ?>
					<tr>
						<th><?=$r['_id']['Action']?> <?=$r['_id']['FirstCurrency']?> with <?=$r['_id']['SecondCurrency']?></th>
						<th style="text-align:right "><?=number_format($r['Amount'],8)?></th>
						<th style="text-align:right "><?=number_format($r['TotalAmount'],8)?></th>						
						<td style="text-align:right "><?=number_format($r['TotalAmount']/$r['Amount'],8)?></td>																		
					</tr>
				<?php }
				}?>
				<?php 
				if(count($TotalCompleteOrders['Sell']['result'])>0){
				foreach ($TotalCompleteOrders['Sell']['result'] as $r){ ?>
					<tr>
						<th><?=$r['_id']['Action']?> <?=$r['_id']['FirstCurrency']?> with <?=$r['_id']['SecondCurrency']?></th>
						<th style="text-align:right "><?=number_format($r['Amount'],8)?></th>
						<th style="text-align:right "><?=number_format($r['TotalAmount'],8)?></th>						
						<td style="text-align:right "><?=number_format($r['TotalAmount']/$r['Amount'],8)?></td>																		
					</tr>
				<?php }
				}?>
		</table>
					</div>
				</div>
			</div>		
		<!-- final summary-->		
<?php 
if($settings['friends']['allow']==true){
?>
		<!-- Friends-->
			<div class="col-md-12">
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">Users you transacted with:<small> You can get alerts when a user places an order</small></h3>
					</div>
					<div class="panel-body">
			<?php foreach($RequestFriends['result'] as $RF){
			$friend = array();
			if($details['Friend']!=""){
				foreach($details['Friend'] as $f){
					array_push($friend, $f);
				}
			}
			if(!in_array($RF['_id']['TransactUsercode'],$friend,TRUE)){
			  ?><a href="/<?=$locale?>/ex/AddFriend/<?=String::hash($RF['_id']['TransactUser_id'])?>/<?=$RF['_id']['TransactUser_id']?>/<?=$RF['_id']['TransactUsercode']?>"
				class=" tooltip-x label label-success" rel="tooltip-x" data-placement="top" title="Add to receive alerts from <?=$RF['_id']['TransactUsercode']?>"
				style="font-weight:bold "><i class="glyphicon glyphicon-plus"></i> <?=$RF['_id']['TransactUsercode']?></a>
			<?php }else{?>
			<a  href="/<?=$locale?>/ex/RemoveFriend/<?=String::hash($RF['_id']['TransactUser_id'])?>/<?=$RF['_id']['TransactUser_id']?>/<?=$RF['_id']['TransactUsercode']?>" class="tooltip-x label label-warning" rel="tooltip-x" data-placement="top" title="Already a friend <?=$RF['_id']['TransactUsercode']?> Remove!">
<i class="glyphicon glyphicon-minus"></i>			<?=$RF['_id']['TransactUsercode']?></a>
			<?php }?>
			<?php }?>
					</div>
				</div>
			</div>		
		<!-- Friends-->		
<?php }?>
		</div>
	</div>
</div>