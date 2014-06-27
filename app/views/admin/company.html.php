<?php
use app\extensions\action\Functions;
$function = new Functions;
?>
<h4>Company</h4>
<table class="table table-condensed table-bordered table-hover" style="font-size:12px ">
<tr>
<th>Username</th>
<th>Company</th>
<th>Short</th>
<th>Address</th>
<th>Country</th>
<th>Registration</th>
<th>URL</th>
<th>Verified</th>
</tr>


<?php foreach( $details as $detail){?>
<tr>
<td><a href="/Admin/companyverify/<?=$detail['_id']?>"><?=$detail['username']?></a></td>
<td><?=$detail['company']['Name']?></td>
<td><?=$detail['company']['ShortName']?></td>
<td><?=$detail['company']['Address']?></td>
<td><?=$detail['company']['Country']?></td>
<td><?=$detail['company']['Registration']?></td>
<td><?=$detail['company']['GovernmentURL']?></td>
<td><strong><?=$detail['company']['verified']?></strong></td>
</tr>
<tr>
	<td colspan="8">
		<table class="table table-condensed table-bordered table-hover" style="font-size:12px ">
			<tr>
				<th>Total Shares</th>
				<td colspan="2"><?=$detail['company']['TotalShares']?></td>
			</tr>
			<tr>
				<th>Block</th>
				<th>Shares</th>
				<th>Price</th>
				<th>Value</th>
			</tr>
			<?php for($i=0;$i<10;$i++){?>
			<tr>
				<td><?=$function->roman($i+1)?></td>
				<td><?=$detail['company']['share'][$i]?></td>
				<td><?=$detail['company']['price'][$i]?></td>				
				<td><?=round($detail['company']['share'][$i]*$detail['company']['price'][$i],0)?></td>				
			</tr>
			<?php 
			$totalShare = $totalShare + $detail['company']['share'][$i];
			$totalValue = $totalValue + round($detail['company']['share'][$i]*$detail['company']['price'][$i],0);			
			}?>
			<tr>
				<td>Total</td>
				<td><?=$totalShare?></td>
				<td><?=round($totalValue/$totalShare,5)?></td>
				<td><?=$totalValue?></td>								
			</tr>
		</table>
	</td>
</tr>
<?php } ?>
</table>