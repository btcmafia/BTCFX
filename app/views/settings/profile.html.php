<h2>Account Profile</h2>
<blockquote class="row"><big>You current email address is <b><?=$emails['Email']?></b></big></blockquote>
<?php if(1 == count($UnvalidatedEmail)) {

 ?>
<div class="row">
<div class="alert alert-danger col-lg-6">
<p>You are in the process of updating your email address to <?=$UnvalidatedEmail['Email']?>, please click the validation link in the email.</p>
<p>Alternatively, <a href="/settings/deleteemail/<?=$UnvalidatedEmail['_id']?>/<?=$UnvalidatedEmail['DeleteCode']?>/" class="alert-link">click here to cancel the update</a>.</p>
</div>
</div>
<?php } else { ?>
<?php echo $this->_render('element', 'settings/email'); ?>
<?php } ?>
