<?=$this->form->create('', array('name' => 'email', 'class' => 'form-horizontal col-lg-4')); ?>
<fieldset>
    <legend><h2>Update Email Address</h2></legend>

<?php
if(isset($error)) { ?>
<div class="alert alert-dismissible alert-danger">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>

<?php } elseif(isset($message)) { ?>
<div class="alert alert-dismissible alert-success">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong> <?=$message?>
</div>
<?php }

//we don't show the form if we have a success message they need to validate the new email
if(! $message) {
 ?>


<div class="form-group">
      <label for="Email" class="col-lg-4 control-label">New Email Address</label>
      <div class="col-lg-8">
        <input class="form-control" id="Email" name="Email" placeholder="New Email Address" type="text">
      </div>
</div>

<div class="form-group">
      <label for="Password" class="col-lg-4 control-label">Password</label>
      <div class="col-lg-8">
        <input class="form-control" id="Password" name="Password" placeholder="Your Password" type="password">
      </div>
</div>
<?php
if(true == $TwoFactorEnabled) {
?>
<div class="form-group">
      <label for="2FA" class="col-lg-4 control-label">Two Factor Code</label>
      <div class="col-lg-8">
        <input class="form-control" id="2FA" name="2FA" placeholder="Enter One Time Password From Your App" type="text">
      </div>
</div>
<?php } ?>

<div class="form-group">
      <div class="col-lg-10 col-lg-offset-4">
        <button type="submit" id="submit-email" value="true" name="submit-email" class="btn btn-primary">Update Email</button>
      </div>
    </div>
<?php } ?>
</fieldset>
<?=$this->form->end();?>
