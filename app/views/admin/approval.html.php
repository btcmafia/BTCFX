
<h3>User Approval</h3>
<div class="col-md-4">
<form name="User_Approval" method="post" action="/Admin/approval" class="form-horizontal">
	<select name="UserApproval" id="UserApproval" class="form-control">
		<option value="All" <?php if($UserApproval=='All'){echo " selected";}?>>All</option>
		<optgroup label="Verified">		
		<option value="VEmail" <?php if($UserApproval=='VEmail'){echo " selected";}?>>Email</option>
		<option value="VPhone" <?php if($UserApproval=='VPhone'){echo " selected";}?>>Mobile/Phone</option>		
		<option value="VBank" <?php if($UserApproval=='VBank'){echo " selected";}?>>Bank Account</option>				
		<option value="VGovernment" <?php if($UserApproval=='VGovernment'){echo " selected";}?>>Government ID</option>				
		<option value="VUtility" <?php if($UserApproval=='VUtility'){echo " selected";}?>>Utility Bill</option>				
		</optgroup>
		<optgroup label="Not Verified">		
		<option value="NVEmail" <?php if($UserApproval=='NVEmail'){echo " selected";}?>>Email</option>
		<option value="NVPhone" <?php if($UserApproval=='NVPhone'){echo " selected";}?>>Mobile/Phone</option>		
		<option value="NVBank" <?php if($UserApproval=='NVBank'){echo " selected";}?>>Bank Account</option>				
		<option value="NVGovernment" <?php if($UserApproval=='NVGovernment'){echo " selected";}?>>Government ID</option>				
		<option value="NVUtility" <?php if($UserApproval=='NVUtility'){echo " selected";}?>>Utility Bill</option>				
		</optgroup>
		<optgroup label="Waiting Verification">		
		<option value="WVEmail" <?php if($UserApproval=='WVEmail'){echo " selected";}?>>Email</option>
		<option value="WVPhone" <?php if($UserApproval=='WVPhone'){echo " selected";}?>>Mobile/Phone</option>		
		<option value="WVBank" <?php if($UserApproval=='WVBank'){echo " selected";}?>>Bank Account</option>				
		<option value="WVGovernment" <?php if($UserApproval=='WVGovernment'){echo " selected";}?>>Government ID</option>				
		<option value="WVUtility" <?php if($UserApproval=='WVUtility'){echo " selected";}?>>Utility Bill</option>				
		</optgroup>
	</select>
		<input type="text" name="UserSearch" id="UserSearch" placeholder="Username" value="" class="form-control">
		<input type="text" name="EmailSearch" id="EmailSearch" placeholder="Email" value="" class="form-control">		
	<input type="submit" value="Go..." class="btn btn-primary btn-block">
</form>
</div>
<table class="table table-condensed table-bordered table-hover" style=" ">
	<tr>
		<th style="text-align:center;">Username</th>
		<th style="text-align:center ">Email</th>
		<th style="text-align:center ">Mobile</th>
		<th style="text-align:center ">Bank</th>		
<?php foreach($settings['documents'] as $documents){
				if($documents['required']==true){?>
		<th width="16%" style="text-align:center "><?=$documents["name"]?></th>		
<?php }
}?>

	</tr>
<?php 
if(count($details)!=0){
$i = 1;
	foreach($details as $detail){
		if($detail['active']=='Yes' || $detail['active']==''){
?>
	<tr>
		<td><?=$i?> 
		<?php 
		if($detail['active']=='Yes' || $detail['active']==''){
		?>
		<a href="/Admin/deactivate/<?=$detail['_id']?>" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Deactivate user account"><i class="glyphicon glyphicon-remove"></i></a>
		<?php }?>
		<?php 
		if($detail['active']=='No'){
		?>
		<a href="/Admin/activate/<?=$detail['_id']?>" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Activate user account"><i class="icon-ok"></i></a>
		<?php }?>		
		<a href="/Admin/detail/<?=$detail['username']?>" target="_blank"><?=$detail['username']?></a></td>
		<td style="text-align:center "><?=$detail['email.verified']?></td>		
		<td style="text-align:center "><?=$detail['phone.verified']?></td>				
		<td style="text-align:center "><a href="/Admin/detail/<?=$detail['username']?>"><?=$detail['bank.verified']?></a></td>						
<?php 
foreach($settings['documents'] as $documents){
				if($documents['required']==true){?>
		<td style="text-align:center "><a href="/Admin/approve/<?=$documents['id']?>/<?=$detail['_id']?>" target="_blank"><?=$detail[$documents["id"].'.verified']?></a></td>
<?php }
}?>
	</tr>

<?php } 
	$i++;	}

} ?>
</table>
