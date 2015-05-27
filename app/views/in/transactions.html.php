<?php
use lithium\util\String;
?>
<h2>Transactions</h2>
	

	<div class="col-md-6">
	<div class="panel panel-info">

		<div class="panel-heading">
			<ul class="nav nav-tabs">
			<li class="active"><a aria-expanded="true" href="#all" data-toggle="tab">All Currencies</a></li>
  			<li class=""><a aria-expanded="false" href="#btc" data-toggle="tab">Bitcoin</a></li>
  			<li class=""><a aria-expanded="false" href="#tcp" data-toggle="tab">Coloured Pound</a></li>
  			<li class=""><a aria-expanded="false" href="#dct" data-toggle="tab">Ducat</a></li>
			</ul>
		</div>


<div id="myTabContent" class="tab-content">


  <div class="tab-pane fade active in" id="all">
 
    
		<table class="table table-striped table-bordered table-hover"  style="font-size:11px "  >
		<thead>
			<tr>
				<th>Date</th>
				<th>Currency</th>				
				<th>Type</th>
				<th>Amount</th>
				<th>Status</th>				
			</tr>
		</thead>
		<tbody>

<?php
$class = 'danger';
$total_amount = 0;
 foreach ($transactions['ALL'] as $tx){

if('cancelled' == $tx['Status']) continue;

if($class == 'danger') $class = 'success';
else $class = 'danger';
?>
                <tr class="<?php echo $class; ?>">
                        <td><?=gmdate('Y-M-d H:i:s',$tx['DateTime']->sec)?></td>
                	<td><?=$tx['Currency']?></td> 
                        <td><?=$tx['Type']?></td>
		        <td style="text-align:right "><?=$tx['Amount']?></td>
			<td><?=$tx['Status']; ?>
<?php if( ($tx['Status'] == 'emailpending') OR ($tx['Status'] == 'processing')) {  ?>
&nbsp;<a title= "Cancel this transaction" href="/in/removetransaction/<?=String::hash($tx['_id'])?>/<?=$tx['_id']?>/transactions/<?=$tx['Currency']?>"><i class="fa fa-times"></i></a>
<?php } ?>
						</td>
               </tr>
<?php } ?>
		</tbody>
	</table>

</div>

  <div class="tab-pane fade" id="btc">
    
		<table class="table table-striped table-bordered table-hover"  style="font-size:11px "  >
		<thead>
			<tr>
				<th>Date</th>
				<th>Currency</th>				
				<th>Type</th>
				<th>Amount</th>
				<th>Status</th>				
			</tr>
		</thead>
		<tbody>

<?php
$class = 'danger';
$total_amount = 0;
 foreach ($transactions['BTC'] as $tx){

if('cancelled' == $tx['Status']) continue;

if($class == 'danger') $class = 'success';
else $class = 'danger';

$total_amount = $tx['Amount'] + $total_amount;
?>
                <tr class="<?php echo $class; ?>">
                        <td><?=gmdate('Y-M-d H:i:s',$tx['DateTime']->sec)?></td>
                	<td><?=$tx['Currency']?></td> 
                        <td><?=$tx['Type']?></td>
		        <td style="text-align:right "><?=$tx['Amount']?></td>
			<td><?=$tx['Status']; ?>
<?php if( ($tx['Status'] == 'emailpending') OR ($tx['Status'] == 'processing')) {  ?>
&nbsp;<a title= "Cancel this transaction" href="/in/removetransaction/<?=String::hash($tx['_id'])?>/<?=$tx['_id']?>/transactions/<?=$tx['Currency']?>"><i class="fa fa-times"></i></a>
<?php } ?>
			</td>
               </tr>
<?php } ?>
                <tr>
                        <td><strong>Total</strong></td>
                        <td colspan=3 style="text-align:right;font-weight:bold;"><?=$total_amount?></td>
                        <td>&nbsp;</td>
                </tr>
		</tbody>
	</table>

  </div>

  <div class="tab-pane fade" id="tcp">
  

		<table class="table table-striped table-bordered table-hover"  style="font-size:11px "  >
		<thead>
			<tr>
				<th>Date</th>
				<th>Currency</th>				
				<th>Type</th>
				<th>Amount</th>
				<th>Status</th>				
			</tr>
		</thead>
		<tbody>
<?php
$class = 'danger';
$total_amount = 0;
 foreach ($transactions['TCP'] as $tx){

if('cancelled' == $tx['Status']) continue;

if($class == 'danger') $class = 'success';
else $class = 'danger';

$total_amount = $tx['Amount'] + $total_amount;
?>
                <tr class="<?php echo $class; ?>">
                        <td><?=gmdate('Y-M-d H:i:s',$tx['DateTime']->sec)?></td>
                	<td><?=$tx['Currency']?></td> 
                        <td><?=$tx['Type']?></td>
		        <td style="text-align:right "><?=$tx['Amount']?></td>
			<td><?=$tx['Status']; ?>
<?php if( ($tx['Status'] == 'emailpending') OR ($tx['Status'] == 'processing')) {  ?>
&nbsp;<a title= "Cancel this transaction" href="/in/removetransaction/<?=String::hash($tx['_id'])?>/<?=$tx['_id']?>/transactions/<?=$tx['Currency']?>"><i class="fa fa-times"></i></a>
<?php } ?>
			</td>
               </tr>
<?php } ?>
                <tr>
                        <td><strong>Total</strong></td>
                        <td colspan=3 style="text-align:right;font-weight:bold;"><?=$total_amount?></td>
                        <td>&nbsp;</td>
                </tr>
		</tbody>
		</table>
</div>

  <div class="tab-pane fade" id="dct">

		<table class="table table-striped table-bordered table-hover"  style="font-size:11px "  >
		<thead>
			<tr>
				<th>Date</th>
				<th>Currency</th>				
				<th>Type</th>
				<th>Amount</th>
				<th>Status</th>				
			</tr>
		</thead>
		<tbody>
<?php
$class = 'danger';
$total_amount = 0;
 foreach ($transactions['DCT'] as $tx){

if('cancelled' == $tx['Status']) continue;

if($class == 'danger') $class = 'success';
else $class = 'danger';

$total_amount = $tx['Amount'] + $total_amount;
?>
                <tr class="<?php echo $class; ?>">
                        <td><?=gmdate('Y-M-d H:i:s',$tx['DateTime']->sec)?></td>
                	<td><?=$tx['Currency']?></td> 
                        <td><?=$tx['Type']?></td>
		        <td style="text-align:right "><?=$tx['Amount']?></td>
			<td><?=$tx['Status']; ?>
<?php if( ($tx['Status'] == 'emailpending') OR ($tx['Status'] == 'processing')) {  ?>
&nbsp;<a title= "Cancel this transaction" href="/in/removetransaction/<?=String::hash($tx['_id'])?>/<?=$tx['_id']?>/transactions/<?=$tx['Currency']?>"><i class="fa fa-times"></i></a>
<?php } ?>
			</td>
               </tr>
<?php } ?>
                <tr>
                        <td><strong>Total</strong></td>
                        <td colspan=3 style="text-align:right;font-weight:bold;"><?=$total_amount?></td>
                        <td>&nbsp;</td>
                </tr>
		</tbody>
		</table>
</div>

</div><!--myTabContent -->
</div>
</div>
