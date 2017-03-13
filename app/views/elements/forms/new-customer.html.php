

<div id="myNav" class="overlay">

  <!-- Button to close the overlay navigation -->
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

<div class="overlay-content">



<?=$this->form->create('', array('class' => 'form-horizontal', 'id' => 'new_customer', 'name' => 'new_customer', 'url' => 'Office::newcustomerandjob')); ?>

<div class="col-lg-8 col-lg-offset-1">


<fieldset>
    <legend><h2 class="">New Customer</h2></legend>

<?php
if(isset($error)) { ?>
<div class="alert alert-dismissible alert-danger col-lg-10 col-lg-offset-1">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>
<?php } elseif(isset($message)) { ?>
<div class="alert alert-dismissible alert-success col-lg-10 col-lg-offset-1">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong> <?=$message?>
</div>
<?php }

?>

<div class="form-group">
      <label for="first_name" class="col-lg-4 control-label">First Name</label>
      <div class="col-lg-6">
        <input class="form-control" id="first_name" name="first_name" placeholder="First Name" type="text">
      </div>
</div>

<?= $this->form->field('first_name', ['type' => 'text']) ?>

<div class="form-group">
      <label for="last_name" class="col-lg-4 control-label">Last Name</label>
      <div class="col-lg-6">
        <input class="form-control" id="last_name" name="last_name" placeholder="Last Name" type="text">
      </div>
</div>

<div class="form-group">
      <label for="phone" class="col-lg-4 control-label">Phone Number</label>
      <div class="col-lg-6">
        <input class="form-control" id="phone" name="phone" placeholder="Phone Number" type="text">
      </div>
</div>

<div class="form-group">
      <label for="email" class="col-lg-4 control-label">Email Address</label>
      <div class="col-lg-6">
        <input class="form-control" id="email" name="email" placeholder="Email Address" type="text">
      </div>
</div>

<div class="form-group">
      <label for="service_address_1" class="col-lg-4 control-label">Address</label>
      <div class="col-lg-6">
        <input class="form-control" id="address_1" name="address_1" placeholder="Address" type="text">
      </div>
</div>

<div class="form-group">
      <label for="service_address_2" class="col-lg-4 control-label">&nbsp;</label>
      <div class="col-lg-6">
        <input class="form-control" id="address_2" name="address_2" placeholder="Address (cont.)" type="text">
      </div>
</div>

<div class="form-group">
      <label for="city" class="col-lg-4 control-label">Town / City</label>
      <div class="col-lg-6">
        <input class="form-control" id="city" name="city" placeholder="Town or City" type="text">
      </div>
</div>

<div class="form-group">
      <label for="postcode" class="col-lg-4 control-label">Postcode</label>
      <div class="col-lg-6">
        <input class="form-control" id="postcode" name="postcode" placeholder="Postcode" type="text">
      </div>
</div>

<div class="form-group">
      <div class="col-lg-6 col-lg-offset-3">
        <button type="submit" id="submit-create-customer" name="submit-create-customer" value="Create Customer" class="btn btn-info">Create Customer & Start a Job</button>
      </div>
</div>
</fieldset>
</div>

<?=$this->form->end();?>
</div>
</div>
