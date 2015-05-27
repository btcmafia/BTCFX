<h2>Verify Your New Email Address</h2>

<?php
if(isset($error)) { ?>
<div class="alert alert-dismissible alert-danger">
  <strong>Error!</strong> <?=$error?>
</div>

<?php } elseif(isset($message)) { ?>
<div class="alert alert-dismissible alert-success">
  <strong>Success!</strong> <?=$message?>
</div>
<?php }


?>
