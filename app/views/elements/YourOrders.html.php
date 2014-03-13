<?php
use lithium\util\String;
?>
	<div class="col-md-4"  style="height:354px;">
		<div class="panel panel-warning">
			<div class="panel panel-heading">
			<h2 class="panel-title"  style="cursor:pointer;font-weight:bold"  onclick="document.getElementById('Graph').style.display='block';">Pending orders <i class="glyphicon glyphicon-indent-left"></i></h2>
			</div>
			<div id="YourOrders" style="height:310px;overflow:auto;padding:0px;margin-top:-20px" class="fade in">			
			<table class="table table-condensed table-bordered table-hover" style="font-size:11px">
				<thead>
					<tr>
						<th style="text-align:center ">Exchange</th>
						<th style="text-align:center ">Price</th>
						<th style="text-align:center ">Amount</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($YourOrders as $YO){ ?>
					<tr>
							<td style="text-align:left ">
							<a href="/ex/RemoveOrder/<?=String::hash($YO['_id'])?>/<?=$YO['_id']?>/<?=$sel_curr?>" title="Remove this order">
								<i class="glyphicon glyphicon-remove"></i></a> &nbsp; 
							<?=$YO['Action']?> <?=$YO['FirstCurrency']?>/<?=$YO['SecondCurrency']?></td>
						<td style="text-align:right "><?=number_format($YO['PerPrice'],4)?>...</td>
						<td style="text-align:right "><?=number_format($YO['Amount'],4)?>...</td>
					</tr>
				<?php }?>					
				</tbody>
			</table>
			</div>
		</div>
	</div>
