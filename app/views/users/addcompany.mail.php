<?php
use app\extensions\action\Functions;
$function = new Functions;
?>
<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="https://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.gif" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi <?=$user['username']?>,</h4>

<p>You have created a company with the details:</p>
<table border="0">
	<tr>
		<td>Company Name:</td>
		<td><?=$data['company']['Name']?></td>
	</tr>
	<tr>
		<td>Company Address:</td>
		<td><?=$data['company']['Address']?></td>
	</tr>
	<tr>
		<td>Country:</td>
		<td><?=$data['company']['Country']?></td>
	</tr>
	<tr>
		<td>Registration Number:</td>
		<td><?=$data['company']['Registration']?></td>
	</tr>
	<tr>
		<td>Government URL:</td>
		<td><?=$data['company']['GovernmentURL']?></td>
	</tr>
	<tr>
		<td>Total Shares:</td>
		<td><?=$data['company']['TotalShares']?></td>
	</tr>
	<tr>
		<td>Verified:</td>
		<td><?=$data['company']['verified']?></td>
	</tr>
</table>
<table border="0">
<tr>
	<th>Block</th>
	<th>Shares</th>
	<th>Price in BTC</th>
</tr>
<?php for($i=0;$i<10;$i++){?>
	<tr>
	<td><?=$function->roman($i+1)?></td>
		<td><?=$data['company']['share'][$i]?></td>
		<td><?=$data['company']['price'][$i]?></td>
	</tr>
<?php } ?>
</table>

<p>Verification usually takes 2 to 7 working days based on the country of registration. You will be informed by email.</p>
<p>Thanks,<br>
<?=NOREPLY?></p>

<p>P.S. Please do not reply to this email. </p>
<p>We do not spam. </p>
