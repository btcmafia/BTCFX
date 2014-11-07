<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="https://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.gif" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi <?=$user['firstname']?>,</h4>

<p>Your withdrawal has been approved <?=COMPANY_URL?>.</p>
<table>
		<tr>
			<td>Amount:</td>
			<td><?=$Transactions['Amount']?></td>
		</tr>
		<tr>
			<td>Withdrawal Charges:</td>
			<td><?=$Transactions['AmountApproved']-$Transactions['Amount']?></td>
		</tr>
		<tr>
			<td>Total:</td>
			<td><?=$Transactions['AmountApproved']?></td>
		</tr>
		<tr>
			<td>Currency:</td>
			<td><?=$Transactions['Currency']?></td>
		</tr>		
</table>
<p>Withdrawal Type: OKPAY (or bank if appropriate)</p>
<p>And the withdrawal charge for OKPAY withdrawal should say 1 (we'll just leave it as 1 of the denomination that is being withdrawn)</p>
<p>Thanks,<br>
<?=NOREPLY?></p>

<p>P.S. Please do not reply to this email. </p>
<p>If you did not initiate this action please contact IBWT as soon as possible via support@ibwt.co.uk or telephone 07914 446125.</p>
<p>We do not spam. </p>