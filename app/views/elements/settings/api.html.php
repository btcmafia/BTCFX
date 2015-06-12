<?=$this->form->create($saved, array('name' => 'api', 'class' => 'form-horizontal col-lg-12')); ?>

<fieldset>
    <legend><h2>API Access</h2></legend>

<?php
if(isset($error_api)) { ?>
<div class="alert alert-dismissible alert-danger">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error_api?>
</div>

<?php } elseif(isset($message_api)) { ?>
<div class="alert alert-dismissible alert-success">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong> <?=$message_api?>
</div>
<?php } ?>

<?php if($APIEnabled) { ?>
<div class="alert alert-success col-lg-10 col-lg-offset-1">
API access is currently enabled with the permissions below. Update the permissions or disable access below.
</div>

<?php } else { ?>

<div class="alert alert-info col-lg-10 col-lg-offset-1">
API access is currently disabled. To enable, set the permissions below and enter your security details.
</div>

<?php } ?>
<div class="form-group">
	<label for="EnableAPI" class="col-sm-offset-2 control-label">Enable API&nbsp;&nbsp;&nbsp;&nbsp;</label>
	<input class="col-sm-offset-2" type="checkbox" name="EnableAPI" value="1" />
</div>

<div class="form-group">
      <label for="AddressesBTC" class="col-lg-8 col-sm-offset-3 control-label">Allowed Withdrawal BTC Addresses</label>
      <div class="col-sm-8 col-sm-offset-4">
        <textarea class="form-control" id="Password" name="AddressesBTC" placeholder="One address per line"></textarea>
      </div>
</div>

<div class="form-group">
      <label for="AddressesCC" class="col-lg-8 col-sm-offset-3 control-label">Allowed Withdrawal Asset Addresses</label>
      <div class="col-lg-8 col-sm-offset-4">
        <textarea class="form-control" id="Password" name="AddressesCC" placeholder="One address per line"></textarea>
      </div>
</div>

<div class="form-group">
      <label for="Password" class="col-lg-4 control-label">Password</label>
      <div class="col-lg-8">
        <input class="form-control" id="Password" name="Password" placeholder="Password" type="password">
      </div>
</div>

<?php if($TwoFactorEnabled) { ?>
<div class="form-group">
      <label for="2FA" class="col-lg-4 control-label">Two Factor Code</label>
      <div class="col-lg-6">
        <input class="form-control" id="2FA" name="2FA" placeholder="One Time Password" type="password">
      </div>
</div>
<?php } ?>


<div class="form-group">
      <div class="col-lg-10 col-lg-offset-4">
        <button type="submit" id="submit-api" value="true" name="submit-api" class="btn btn-primary">Update API Settings</button>
      </div>
    </div>
</fieldset>
<?=$this->form->end();?>


