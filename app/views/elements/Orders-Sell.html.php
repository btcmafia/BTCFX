	<div class="col-md-6">
		<div class="panel panel-success">
			<div class="panel panel-heading">
			<h2 class="panel-title"  style="font-weight:bold" href="#">Orders:
			Sell <?=$first_curr?> &gt; <?=$second_curr?></h2>
<?php  foreach($TotalSellOrders['result'] as $TSO){
	$SellAmount = $TSO['Amount'];
	$SellTotalAmount = $TSO['TotalAmount'];
}?>			
			</div>
		<div id="SellOrders" style="margin-top:-20px ">
			<table class="table table-condensed table-bordered table-hover" style="font-size:12px ">
				<thead>
					<tr>
					<th style="text-align:center " rowspan="2">#</th>					
					<th style="text-align:center " >Price</th>
					<th style="text-align:center " ><?=$first_curr?></th>
					<th style="text-align:center " ><?=$second_curr?></th>					
					</tr>
					<tr>
					<th style="text-align:center " >Total &raquo;</th>
					<th style="text-align:right " ><?=number_format($SellAmount,8)?></th>
					<th style="text-align:right " ><?=number_format($SellTotalAmount,8)?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$SellOrderAmount = 0; $FillSellOrderAmount =0;
					foreach($SellOrders['result'] as $SO){
						if($user['_id']!=$SO['_id']['user_id']){
							$FillSellOrderAmount = $FillSellOrderAmount + round($SO['Amount'],8);
						}
						$SellOrderPrice = round($SO['_id']['PerPrice'],8);
						$SellOrderAmount = $SellOrderAmount + round($SO['Amount'],8);
						?>
					<tr onClick="SellOrderFill(<?=$SellOrderPrice?>,<?=$FillSellOrderAmount?>);"  style="cursor:pointer" 
					 class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Buy <?=$SellOrderAmount?> <?=$first_curr?> at <?=$SellOrderPrice?> <?=$second_curr?>">
						<td style="text-align:right"><?=$SO['No']?></td>											
						<td style="text-align:right"><?=number_format(round($SO['_id']['PerPrice'],8),8)?></td>						
						<td style="text-align:right"><?=number_format(round($SO['Amount'],8),8)?></td>
						<td style="text-align:right"><?=number_format(round($SO['Amount']*$SO['_id']['PerPrice'],8),8)?></td>
					</tr>
					<?php }?>
				</tbody>
			</table>
		</div>
	</div>
</div>