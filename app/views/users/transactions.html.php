<div class="row">
	<div class="col-md-6">
	<div class="panel panel-info">
		<div class="panel-heading">
		<h2 class="panel-title" >Transactions in BTC</h2>
		</div>
		<table class="table table-condensed table-bordered table-hover" style="font-size:11px " >
		<thead>
			<tr>
				<th>Date</th>
				<th>Amount BTC</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($transactions as $tx){?>
		<tr <?php ?> style="background-color:#669933 "></tr>
			<td><?=gmdate('Y-M-d H:i:s',$tx['DateTime']->sec)?></td>
			<td style="text-align:right "><?=number_format($tx['Amount'],8)?></td>
			<td><?php if($tx['Added']==true){echo "Deposit";}else{echo "Withdraw";}?></td>
		</tr>
<?php 
	$Amount = $Amount + number_format($tx['Amount'],8);
} ?>
		<tr>
			<th >Total</th>
			<td style="text-align:right "><?=number_format($Amount,8)?></td>
			<td></td>
		</tr>
		</tbody>
	</table>
		</div>
	</div>
	<div class="col-md-6">
	<div class="panel panel-info">
		<div class="panel-heading">
		<h2 class="panel-title" >Transactions in other currencies</h2>
		</div>
		<table class="table table-condensed table-bordered table-hover"  style="font-size:11px "  >
		<thead>
			<tr>
				<th>Date</th>
				<th>Amount</th>
				<th>Currency</th>				
				<th>Type</th>
				<th>Approved</th>				
			</tr>
		</thead>
		<tbody>
<?php 
foreach ($Fiattransactions as $tx){?>
		<tr <?php ?> style="background-color:#669933 "></tr>
			<td><?=gmdate('Y-M-d H:i:s',$tx['DateTime']->sec)?></td>
			<td style="text-align:right "><?=number_format($tx['Amount'],2)?></td>
			<td style="text-align:right "><?=$tx['Currency']?></td>			
			<td><?php if($tx['Added']==true){echo "Deposit";}else{echo "Withdraw";}?></td>
			<td style="text-align:center"><?=$tx['Approved']?></td>			

		</tr>
<?php 
} ?>
		</tbody>
	</table>
			
	</div>
</div>