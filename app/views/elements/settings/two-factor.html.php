<?=$this->form->create('', array('class' => 'form-horizontal', 'name' => '2fa', 'url' => 'Settings::security')); ?>

<fieldset>
    <legend><h2>Two Factor Authentication</h2></legend>

<?php
if(isset($error_2fa)) { ?>
<div class="alert alert-dismissible alert-danger col-lg-10 col-lg-offset-1">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error_2fa?>
</div>
<?php } elseif(isset($message_2fa)) { ?>
<div class="alert alert-dismissible alert-success col-lg-10 col-lg-offset-1">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong> <?=$message_2fa?>
</div>
<?php }

if($TwoFactorEnabled) {
?>

<div class="alert alert-success col-lg-10 col-lg-offset-1">
Two factor authentication is currently enabled. To disable, enter your password and two factor code below.
</div>


<div class="form-group">
      <label for="Password" class="col-lg-4 control-label">Password</label>
      <div class="col-lg-6">
        <input class="form-control" id="Password" name="Password" placeholder="Password" type="password">
      </div>
</div>

<div class="form-group">
      <label for="2FA" class="col-lg-4 control-label">Two Factor Code</label>
      <div class="col-lg-6">
        <input class="form-control" id="2FA" name="2FA" placeholder="One Time Password" type="password">
      </div>
</div>
<div class="form-group">
      <div class="col-lg-6 col-lg-offset-4">
        <button type="submit" id="submit-2fa" name="submit-2fa" value="submit-2fa" class="btn btn-danger">Disable Two Factor Authentication!</button>
      </div>
</div>

<?php } else { ?>


<div class="alert alert-danger col-lg-11 col-lg-offset-1">
Two factor authentication is currently disabled. To enable, scan the QR code below using your authenticator app, then enter the generated code and your password below.
</div>

<div class="row">

<div class="col-sm-10">

<div class="form-group">
      <label for="Password" class="col-sm-5 control-label">Password</label>
      <div class="col-sm-7">
        <input class="form-control" id="Password" name="Password" placeholder="Password" type="password">
      </div>
</div>

<div class="form-group">
      <label for="2FA" class="col-sm-5 control-label">Two Factor Code</label>
      <div class="col-sm-7">
        <input class="form-control" id="2FA" name="2FA" placeholder="One Time Password" type="password">
      </div>
</div>



<div class="form-group">
      <div class="col-lg-6 col-lg-offset-4">
        <button type="submit" id="submit-2fa" name="submit-2fa" value="submit-2fa" class="btn btn-success">Enable Two Factor Authentication</button>
      </div>
</div>

</div>

<div class="col-sm-2">
<img src="<?=$qrcode?>" width="140" height="140" title="Scan with your app to generate one time passwords" alt="QR Code" />
</div>

</div><!--row-->

<?php } ?>

<input type="hidden" name="key" id="key" value="<?=$key?>" />
</fieldset>

<?=$this->form->end();?>


