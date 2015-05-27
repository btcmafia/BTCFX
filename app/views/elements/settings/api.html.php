<?=$this->form->create($saved, array('name' => 'email', 'class' => 'form-horizontal col-lg-12')); ?>

<fieldset>
    <legend><h2>API Access</h2></legend>

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

<?php if($APIEnabled) { ?>
<div class="alert alert-success col-lg-10 col-lg-offset-1">
API access is currently enabled with the permissions below. Update the permissions or disable access below.
</div>

<?php } else { ?>

<div class="alert alert-success col-lg-10 col-lg-offset-1">
API access is currently disabled. To enable, set the permissions below and enter your security details.
</div>

<?php } ?>

<div class="form-group">
      <div class="col-lg-10 col-lg-offset-4">
        <button type="submit" id="submit-api" value="true" name="submit-api" class="btn btn-primary">Update API Settings</button>
      </div>
    </div>
</fieldset>
<?=$this->form->end();?>


