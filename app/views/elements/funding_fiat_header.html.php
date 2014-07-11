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
		}elseif($details[$documents['id'].'.verified']=='No'){
				$alldocuments[$documents['id']]="Pending";
		}else{
				$alldocuments[$documents['id']]="Yes";
		}
	}
}
$all = true;
foreach($alldocuments as $key=>$val){						
	if($val!='Yes'){
	$all = false;
	break;
	}
}
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-info" >
			<div class="panel-heading">
				<?php if(!$all){?>
				<h2 class="panel-title"  >Verification incomplete!</h2>
				<?php }else{?>
				<h2 class="panel-title"  ><?=$currency?> Deposits / Withdrawals</h2>
				<?php }?>
			</div>
		</div>
		<div class="panel-body">		
		<table class="table table-condensed ">
			<tr>
			<td>
<!-----Bank Details start----->					
	<?php if(strlen($details['bank']['verified'])===0){?>
	<a href="/users/settings/bank" class="tooltip-x" rel="tooltip-x" data-placement="top" title="Compulsary to transact!"><i class="icon-remove icon-black"></i>Address & Bank</a>
	<?php }elseif($details['bank']['verified']=='No'){?>
		<a href="#" class="tooltip-x" rel="tooltip-x" data-placement="top" title="Pending verification!"><i class="icon-edit icon-black"></i>Address & Bank</a>
	<?php }else{ ?>
		<a href="#" class="tooltip-x" rel="tooltip-x" data-placement="top" title="Completed!"><i class="icon-ok icon-black"></i> Address & Bank</a>					
	<?php }	?>
			</td>
			</tr>
			<tr>
			<td>
<!-----Bank Details end----->					

<!-----Government Details start----->					
	<?php 
	if(strlen($details['government.verified'])==0){
	?>
		<a href="/users/settings/government" class="label label-warning tooltip-x" rel="tooltip-x" data-placement="top" title="Compulsary to transact!"><i class="icon-remove icon-black"></i> Government Photo ID</a>
	<?php }elseif($details['government.verified']=='No'){?>
		<a href="#" class="label label-important tooltip-x" rel="tooltip-x" data-placement="top" title="Pending verification!"><i class="icon-edit icon-black"></i> Government Photo ID</a>
	<?php }else{ ?>
		<a href="#" class="label label-success tooltip-x" rel="tooltip-x" data-placement="top" title="Completed!"><i class="icon-ok icon-black"></i> Government ID</a>					
	<?php }	?>
			</td>
			</tr>
			<tr>
			<td>
<!-----Government Details end----->					

<!-----Utility Details start----->					
	<?php 
	if(strlen($details['utility.verified'])==0){
	?>
		<p href="/users/settings/utility" class="label label-warning tooltip-x" rel="tooltip-x" data-placement="top" title="Compulsary to transact!"><i class="icon-remove icon-black"></i> Proof of Address</p>
	<?php }elseif($details['utility.verified']=='No'){?>
		<a href="#" class="label label-important tooltip-x" rel="tooltip-x" data-placement="top" title="Pending verification!"><i class="icon-edit icon-black"></i> Utility Bill</a>
	<?php }else{ ?>
		<a href="#" class="label label-success tooltip-x" rel="tooltip-x" data-placement="top" title="Completed!"><i class="icon-ok icon-black"></i> Utility Bill</a>					
	<?php }	?>
<!-----Utility Details end----->				
			</td>
			</tr>
		</table>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-info" >
			<div class="panel-heading">OK Pay</div>
		</div>
		<span class="pull-right" style="margin-top:-10px ">
		<!-- Begin OKPAY Logo --><A HREF="https://www.okpay.com/?rbp=IBWT" target="_blank"><IMG SRC="https://www.okpay.com/img/partners/rbp_banner.gif" BORDER="0" ALT="Sign up for OKPAY and start accepting payments instantly."></A><!-- End OKPAY Logo -->
		<p style="width:460px;text-align:center">For deposits and withdrawals via bank wire transfers please see our <a href="/okpay">OKPAY</a> information page, or visit <a href="/okpay">OKPAY</a> to create and account today!</p>
		</span>
	</div>
</div>