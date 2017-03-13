<?=$this->form->create('', array('class' => 'form-horizontal col-lg-12')); ?>

<fieldset>
    <legend><h2>Reset Password</h2></legend>


<?php
if(isset($error)) { ?>
<div class="alert alert-dismissible alert-danger">
  <strong>Error!</strong> <?=$error?>
</div>

<?php } elseif(isset($message)) { ?>
<div class="alert alert-dismissible alert-success">
  <strong>Success!</strong> <?=$message?>
</div>
<?php } ?>

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

<div class="form-group">
      <div class="col-lg-10 col-lg-offset-4">
        <button type="submit" id="submit" value="true" name="submit" class="btn btn-primary">Reset Password</button>
      </div>
    </div>
<input type="hidden" name="code" id="code" value="<?=$code?>" />
<input type="hidden" name="email" id="email" value="<?=$email?>" />
<input type="hidden" name="user_id" id="user_id" value="<?=$user_id?>" />
</fieldset>
<?=$this->form->end();?>





