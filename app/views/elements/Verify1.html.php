	<div class="col-md-6">
		<div class="panel panel-info" style="min-height:350px ">
			<div class="panel-heading">
			<h2 class="panel-title"  style="cursor:pointer;font-weight:bold" onclick="document.getElementById('Graph').style.display='block';">No funds in <?=$second_curr?> / Verification
<i class="glyphicon glyphicon-indent-left"></i></h2>
			</div>
<table class="table table-condensed" height:"334px">
	<?php 
	$alldocuments = array();
	?>
	<tr>
		<td colspan="2">
			You should verify:
		</td>
	</tr>
		<?php 
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
	<tr>
		<td colspan="2">
					<a href="/users/settings/<?=$documents['id']?>" class="label label-warning tooltip-x" rel="tooltip-x" data-placement="top" title="Compulsary to transact!"><i class="glyphicon glyphicon-remove"></i> <?=$name?></a>				
		</td>
		</tr>
					<?php }elseif($details[$documents['id'].'.verified']=='No'){
							$alldocuments[$documents['id']]="Pending";
					?>
	<tr>
		<td colspan="2">
	<a href="#" class="label label-danger tooltip-x" rel="tooltip-x" data-placement="top" title="Pending verification!"><i class="glyphicon glyphicon-edit"></i> <?=$name?></a>
		</td>
		</tr>
					<?php }else{
						$alldocuments[$documents['id']]="Yes";
					?>
	<tr>
		<td colspan="2">
					<a href="#" class="label label-success tooltip-x" rel="tooltip-x" data-placement="top" title="Completed!"><i class="glyphicon-ok glyphicon"></i> <?=$name?></a>					
		</td>
		</tr>
	<?php }
			}
			$i++;
		}
	$all = false;
		foreach($alldocuments as $key=>$val){						
			if($val=='Yes'){
			$all = true;
			}else{
			$all = false;	
			break;
			}
		}
			$first_curr = strtoupper(substr($currencyStatus['trade'],0,3));
			$second_curr = strtoupper(substr($currencyStatus['trade'],4,3));			
			$VirtualCurr = array(); $FiatCurr = array();
			if($currencyStatus['FirstType']=='Virtual'){
				array_push($VirtualCurr,$first_curr);
				}else{
				array_push($FiatCurr,$first_curr);
			}
			if($currencyStatus['SecondType']=='Virtual'){
				array_push($VirtualCurr,$second_curr);
				}else{
				array_push($FiatCurr,$second_curr);
			}
		
		?>
	<tr>
		<td colspan="2">If all the above are verified, add Virtual or Fiat currency through the link below:	</td>
	</tr>
	<tr>
<?php foreach($VirtualCurr as $VC)	{?>
		<td><a href="/users/funding/<?=strtolower($VC)?>" class="btn btn-primary">Funding <?=$VC?></a></td>
<?php }?>		
	</tr>
	<tr>
<?php if($all){

	if(count($FiatCurr)!=0)	{?>
		<td colspan="2"><a href="/users/funding_fiat/<?=$FiatCurr[0]?>" class="btn btn-primary">Funding <?=$FiatCurr[0]?></a></td>	
<?php }?>		
	</tr>
<?php }?>	
</table>			
	</div>
</div>
