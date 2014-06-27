<?php
use app\extensions\action\Functions;
$function = new Functions;
?>
<h4><?=$company['company']['Name']?></h4>
<h3>Buy shares of <?=$company['company']['Name']?> (<?=$company['company']['ShortName']?>) with BTC</h3>
<?php
$BalanceFirst = $details['balance'][$first_curr];
if (is_null($BalanceFirst)){$BalanceFirst = 0;}
$$first_curr = $details['balance'][$first_curr];
for($i=0;$i<10;$i++){
	if($company['company']['share'][$i]-$company['company']['sold'][$i]>0){
		break;
	}
}
$CurrentPrice = $company['company']['price'][$i];
?>
<div class="row" >
	<div class="col-md-6">
		<div class="panel panel-info" >
			<div class="panel-heading">
				<h2 class="panel-title"  style="cursor:pointer;font-weight:bold">Buy <?=$second_curr?> with <?=$first_curr?></h2>
			</div>
	<?=$this->form->create(null,array('id'=>'BuyShares')); ?>
	<table class="table table-condensed " >
		<tr>
			<td width="50%">Your balance:<br>
			<span class="btn btn-info btn-block" ><span id="BalanceFirst"><?=$BalanceFirst?></span> <?=$first_curr?></span>
			</td>
			<td>Current Price / Share<br>
			<span class="btn btn-warning btn-block" ><span id="CurrentPrice"><?=$CurrentPrice?></span> <?=$second_curr?></span>
			</td>
		</tr>
		<tr>
			<td>
			<?=$this->form->field('BuyAmount', array('label'=>'Shares '.$second_curr,'class'=>'col-md-1 form-control numbers', 'value'=>0, 'onBlur'=>'$("#BuyShareButton").attr("disabled", "disabled");','min'=>'0','max'=>'99999999','maxlength'=>'10')); ?>				
			</td>
			<td>
				<label for="SharesAvailable">Shares Available of <?=$second_curr?></label>
			<div class="input-group">
			<?=$company['company']['share'][$i]-$company['company']['sold'][$i];?> from Block <?=$function->roman($i+1)?>
			</div>				
			</td>				
		</tr>
		<tr>
			<td>Total: </td>
			<td> <span class="label label-warning"><span id="BuyTotal">0</span> <?=$first_curr?></span></td>
		</tr>
		<tr>
			<td>Fees: </td>
			<td> <span class="label label-success"><span id="BuyFee">0</span> <?=$first_curr?></span></td>
		</tr>
		<tr>
			<td colspan="2"  style="height:50px "><span id="BuySummary">Summary of your order</span></td>
		</tr>
		<tr>
			<td><input type="button" onClick="BuyShareCalculate()" class="btn btn-warning btn-block" value="Calculate"></td>
			<td><input type="submit" id="BuyShareButton" class="btn btn-primary btn-block" disabled="disabled" value="Submit" onClick='$("#BuyShareButton").attr("disabled", "disabled");$("#BuyForm").submit();'></td>
		</tr>
	</table>
	<?=$this->form->end(); ?>
		</div>
	</div>
</div>