
<?=$this->form->create('', array('class' => 'form-horizontal col-lg-12')); ?>

<fieldset>
    <legend><h2>Update Password</h2></legend>

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
<?php } ?>

<div class="form-group">
      <label for="OldPassword" class="col-lg-4 control-label">Old Password</label>
      <div class="col-lg-8">
        <input class="form-control" id="OldPassword" name="OldPassword" placeholder="" type="password">
      </div>
</div>

<div class="form-group">
      <label for="NewPassword" class="col-lg-4 control-label">New Password</label>
      <div class="col-lg-8">
        <input class="form-control" id="NewPassword" name="NewPassword" placeholder="" type="password">
      </div>
</div>

<div class="form-group">
      <label for="ConfirmPassword" class="col-lg-4 control-label">Confirm Password</label>
      <div class="col-lg-8">
        <input class="form-control" id="ConfirmPassword" name="ConfirmPassword" placeholder="" type="password">
      </div>
</div>
<?php
if(true == $TwoFactorEnabled) { 
?>
<div class="form-group">
      <label for="2FA" class="col-lg-4 control-label">Two Factor Code</label>
      <div class="col-lg-8">
        <input class="form-control" id="2FA" name="2FA" placeholder="" type="password">
      </div>
</div>
<?php } ?>

<div class="form-group">
      <div class="col-lg-10 col-lg-offset-4">
        <button type="submit" id="submit-password" value="true" name="submit-password" class="btn btn-primary">Update Password</button>
      </div>
    </div>
<input type="hidden" name="key" id="key" value="<?=$key?>" />
</fieldset>
<?=$this->form->end();?>
