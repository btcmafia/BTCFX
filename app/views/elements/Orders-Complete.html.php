		<div class="col-md-6">
			<div class="panel panel-success">
				<div class="panel-heading">
				<h2 class="panel-title"  style="font-weight:bold" >Completed orders</h2>
				</div>
				<div id="YourCompleteOrders" style="overflow:auto;">	
			<table class="table table-condensed table-bordered table-hover" style="font-size:11px">
				<thead>
					<tr>
						<th style="text-align:center ">Exchange</th>
						<th style="text-align:center ">Price</th>
						<th style="text-align:center ">Amount</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($YourCompleteOrders as $YO){ ?>
					<tr style="cursor:pointer"
					class=" tooltip-x" rel="tooltip-x" data-placement="top" title="<?=$YO['Action']?> <?=number_format($YO['Amount'],3)?> at 
					<?=number_format($YO['PerPrice'],8)?> on <?=gmdate('Y-m-d H:i:s',$YO['DateTime']->sec)?>">
						<td style="text-align:left ">
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
