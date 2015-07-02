<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="<?=PROTOCOL?>://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.gif" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi <?=$details['username']?>,</h4>
<p>You have requested to withdraw <strong><?=abs($data['Amount'])?> <?=$data['Currency']?></strong> from <?=COMPANY_URL?>.</p>
<p>Click on the link below to confirm the transfer. </p>
<p>If you did not authorize this withdrawal to address <strong><?=$address?></strong> please <strong style="color:#FF0000">do not</strong> click on the link.</p>
<a href="<?=PROTOCOL?>://<?=COMPANY_URL?>/in/paymentconfirm/<?=$data['Currency']?>/<?=$data['verify.payment']?>"><?=PROTOCOL?>://<?=COMPANY_URL?>/in/paymentconfirm/<?=$data['Currency']?>/<?=$data['verify.payment']?></a>

<p>You will be asked for your main password on the page following the link to authorize the transfer. This is an added security feature we employ to secure your coins from hackers / spammers.</p>

<p>Thanks,<br>
<?=NOREPLY?></p>

<p>P.S. Please do not reply to this email. </p>
