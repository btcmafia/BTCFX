<?php
use app\extensions\action\Functions;
$function = new Functions;
?>
<?php
	if($details['company']['verified']=="Yes"){
		$disabled = 'true';				
	}else{
		$disabled = '';				
	}
?>
<h2>Company details</h2>
<div class="col-sm-12 col-md-12">
<?=$this->form->create("",array('url'=>'/users/addcompany')); ?>
<?=$this->form->field('Name', array('label'=>'', 'placeholder'=>'Company Name','value'=>$details['company']['Name'],'class'=>'form-control')); ?><br>
<?=$this->form->field('ShortName', array('label'=>'', 'placeholder'=>'Short Name: UPPER CASE - Max 5 characters','value'=>$details['company']['ShortName'],'class'=>'form-control','readonly'=>$disabled)); ?><br>
<?=$this->form->field('Address', array('label'=>'', 'placeholder'=>'Company Address','value'=>$details['company']['Address'],'class'=>'form-control')); ?><br>
<?=$this->form->field('Country', array('label'=>'', 'placeholder'=>'Company Country','value'=>$details['company']['Country'],'class'=>'form-control')); ?><br>
<?=$this->form->field('Registration', array('label'=>'', 'placeholder'=>'Company Registration Number','value'=>$details['company']['Registration'],'class'=>'form-control')); ?><br>
<?=$this->form->field('GovernmentURL', array('label'=>'', 'placeholder'=>'URL of Government website to verify','value'=>$details['company']['GovernmentURL'],'class'=>'form-control')); ?><br>
<?=$this->form->field('TotalShares', array('label'=>'', 'placeholder'=>'Total Shares','value'=>$details['company']['TotalShares'],'class'=>'form-control','readonly'=>$disabled)); ?><br>
<h4>Sell Shares</h4>
<div class="col-sm-12 col-md-12">
<table class="table table-condensed table-stripped table-bordered" >
<tr>
	<th>Block</th>
	<th>Shares</th>
	<th>Price in BTC</th>
	<th>Value in BTC</th>
	<th>Sold</th>
</tr>
<?php for($i=0;$i<10;$i++){
				if($details['company']['share'][$i]==""){
					$Share = 10000;
				}else{
					$Share = $details['company']['share'][$i];
				}
				if($details['company']['price'][$i]==""){
					$Price = 0.01+0.01/10*$i;
				}else{
					$Price = $details['company']['price'][$i];
				}
				if($details['company']['sold'][$i]>0){
					$disabled = 'true';
				}else{
					$disabled = '';
				}
?>
	<tr>
	<td><?=$function->roman($i+1)?></td>
		<td><?=$this->form->field('share.'.$i, array('label'=>'', 'placeholder'=>'Shares','value'=>$Share,'class'=>'form-control','onblur'=>'CalculateShares();','readonly'=>$disabled)); ?>
		</td>
		<td><?=$this->form->field('price.'.$i, array('label'=>'', 'placeholder'=>'Price','value'=>$Price,'class'=>'form-control','onblur'=>'CalculateShares();','readonly'=>$disabled)); ?></td>
		<td id="Cost<?=$i?>"><?=$Share*$Price?></td>
		<td><?=$this->form->field('sold.'.$i, array('readonly'=>$disabled,'label'=>'','value'=>$details['company']['sold'][$i])); ?></td>
	</tr>
	
<?php 
$totalShares = $Share + $totalShares;
$totalBTC = $Price * $Share + $totalBTC;

}?>
	<tr>
	<th>Total</th>
	<th>Shares</th>	
	<th>Avg price</th>
	<th>Total BTC</th>
	</tr>
	<tr>
	<td></td>
	<td id="TotalShare"><?=$totalShares?></td>
	<td id="AvgPriceShare"><?=round($totalBTC/$totalShares,6)?></td>
	<td id="TotalValue"><?=$totalBTC?></td>	
	</tr>
</table>
<p class="btn <?php if($details['company']['verified']=="Yes"){echo 'btn-success';}else{echo 'btn-danger';}?>">
<?php if($details['company']['verified']=="Yes"){?>
Your company is verified. If you modify, verification process will take 2 to 7 working days.
<?php }else{?>
Verification usually takes 2 to 7 working days based on the country of registration. You will be informed by email.
<?php }?>
</p><br><br>
<p>After verification is complete, you will not be able to change the number of shares and the price of the block which has been partially sold. </p>
</div>
<input type="submit" value="Save Company" class="btn btn-primary">
</form>

</div>
