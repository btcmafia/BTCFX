
<h2>Thanks for signing up!</h2>
<div class="row">
<?php
if(isset($error)) { ?>
<div class="alert alert-dismissible alert-danger col-sm-5">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>
<?php } elseif(isset($message)) { ?>
<div class="alert alert-dismissible alert-success col-sm-5">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong> <?=$message?>
</div>
<?php } ?>
</div>


<div class="row">
<div class="alert alert-info col-sm-5">
<p>Please verify your email address by clicking the link in the email we just sent you.</p>
<p>Please check your spam folder if you can't find it!</p> 
</div>
</div>

<div class="row"><a href="/register/resend/<?=$user_id?>/">Click here to resend the verification email.</a></div>
