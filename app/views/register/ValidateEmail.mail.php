<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="<?=PROTOCOL?>://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.gif" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi <?=$username?>,</h4>

<p>Thanks for registering at <?=COMPANY_NAME?></p>
<p>Please click the link below to verify your email address:</p>

<p><a href="<?=PROTOCOL?>://<?=$_SERVER['HTTP_HOST'];?>/settings/verifyemail/<?=$user_id?>/<?=$email?>/<?=$verify_code?>">
<?=PROTOCOL?>://<?=$_SERVER['HTTP_HOST'];?>/settings/verifyemail/<?=$user_id?>/<?=$email?>/<?=$verify_code?></a></p>

<p>Thanks,<br>
<?=NOREPLY?></p>

<p>P.S. Please do not reply to this email. </p>
