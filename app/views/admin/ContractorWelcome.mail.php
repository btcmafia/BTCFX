<h1><?=COMPANY_NAME?></h1>
<h4>Hi <?=$firstname?>,</h4>

<p>We have created a contractor account for you at <?=COMPANY_NAME?>. Your username is <b><?=$username?></b>.</p>

<p>You can use this account to let us know your availability for jobs. Click the link below to reset your password and get started.</p>

<p>For futher information about how it works and why <a href='http://sjnice.com/using-the-contractors-web-app/'>please see this blog post.</a></p>

<p>
<a href="<?=PROTOCOL?>://<?=$_SERVER['HTTP_HOST'];?>/in/changepassword/<?=$verify_code?>">
         <?=PROTOCOL?>://<?=$_SERVER['HTTP_HOST'];?>/in/changepassword/<?=$verify_code?>
</a>
</p>


<p>The link above will expiry after 7 days, after that you can always request a new reset link by visiting the <a href="<?=PROTOCOL?>://<?=$_SERVER['HTTP_HOST'];?>/in/forgotpassword/">forgot password page</a>. Any problems please reply to this email.</p>

<p>Thanks,<br>
<p>Stephen &amp; Maria
<br /><?=COMPANY_NAME?>
</p>

<p>P.S. The website is in very early beta mode so things might not work exactly as they should. Please report any bugs found by replying to this email.</p>
