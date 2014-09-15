<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="https://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.gif" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi Admin,</h4>
<p><?=$data['username']?> has requested to withdraw <strong><?=abs($data['Amount'])?> <?=$data['Currency']?></strong> from <?=COMPANY_URL?>.</p>
<p>Click on the link below to initiate the transfer. </p>
<p>If you did not authorize this withdrawal to the address: <strong><?=$data['address']?></strong> please <strong style="color:#FF0000">do not</strong> click on the link.</p>
<a href="https://<?=COMPANY_URL?>/users/paymentadminconfirm/<?=$data['Currency']?>/<?=$data['verify']?>">https://<?=COMPANY_URL?>/users/paymentadminconfirm/<?=$data['Currency']?>/<?=$data['verify']?></a>

<p>Thanks,<br>
<?=NOREPLY?></p>

<p>P.S. Please do not reply to this email. </p>
<p>This email was sent to you as you tried to withdraw BTC from <?=COMPANY_URL?> with the email address. 
<p>We do not spam. </p>
