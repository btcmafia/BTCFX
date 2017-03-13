<?=$this->form->create('', array('name' => 'email', 'class' => 'form-horizontal col-lg-4')); ?>
<fieldset>
    <legend><h2>Create Contractor</h2></legend>

<?php
if(isset($error)) { ?>
<div class="alert alert-dismissible alert-danger">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>

<?php } 
if(isset($message)) { ?>
<div class="alert alert-dismissible alert-success">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong> <?=$message?>
</div>
<?php }

//we don't show the form if we have a success message
if(! $message) {
 ?>


<div class="form-group">
      <label for="firstname" class="col-lg-4 control-label">First name</label>
      <div class="col-lg-8">
        <input class="form-control" id="firstname" name="firstname" type="text">
      </div>
</div>

<div class="form-group">
      <label for="lastname" class="col-lg-4 control-label">Last name</label>
      <div class="col-lg-8">
        <input class="form-control" id="lastname" name="lastname" type="text">
      </div>
</div>

<div class="form-group">
      <label for="trading_name" class="col-lg-4 control-label">Trading name</label>
      <div class="col-lg-8">
        <input class="form-control" id="trading_name" name="trading_name" type="text">
      </div>
</div>

<div class="form-group">
      <label for="username" class="col-lg-4 control-label">Username</label>
      <div class="col-lg-8">
        <input class="form-control" id="username" name="username" type="text">
      </div>
</div>

<div class="form-group">
      <label for="email" class="col-lg-4 control-label">Email address</label>
      <div class="col-lg-8">
        <input class="form-control" id="email" name="email" type="text">
      </div>
</div>

<div class="form-group">
      <label for="mobile" class="col-lg-4 control-label">Mobile number</label>
      <div class="col-lg-8">
        <input class="form-control" id="mobile" name="mobile" type="text">
      </div>
</div>

<div class="form-group">
      <div class="col-lg-10 col-lg-offset-4">
        <button type="submit" id="submit" value="true" name="submit" class="btn btn-primary">Create Contractor</button>
      </div>
    </div>
<?php } ?>
</fieldset>
<?=$this->form->end();?>

