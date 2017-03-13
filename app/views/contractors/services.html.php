<h2 class="title"><?=$title?></h2>


<div class="alert alert-info">
  <p>Select the types of jobs you are willing to do below. Make sure you click the update button to save your changes.</p>
<!--  <p><strong>Coming soon: </strong> You'll be able to set a different minimum hourly rate for each type of job.</p> --> 
</div>

<?php

if(0 == count($services)) {

$error = "<p>You haven't been approved for any services yet. Don't worry, Maria is probably updating your profile right now.</p>

	  <p>Please check back in 10 minutes. If it isn't fixed by then you might want to get in touch.</p>"; 

}


if(isset($error)) { ?>
<div class="alert alert-dismissible alert-danger">
  <strong>Error!</strong> <?php echo $error; ?>
</div>

<?php } elseif(isset($message)) { ?>
<div class="alert alert-dismissible alert-success">
  <strong>Success!</strong> <?=$message?>
</div>
<?php } 




if(0 != count($services)) {


echo $this->form->create('', array('class' => 'form-horizontal', 'name' => 'update-contractor-active-services'));

foreach($services as $trade => $data) {

//only one trade so no heading required
//echo "<h2>$trade</h2>"; 


foreach($data as $service_category => $foo) {

echo "<h3>$service_category</h3>";

foreach($foo as $foo) {

$service_id = $foo['service_id'];
$service_name = $foo['service_name'];

if($foo['active']) $class = 'active-service';
else $class = 'inactive-service';

echo "<div class='form-group $class'>";
echo "<label for='$service_id' class='col-lg-2 control-label'>$service_name</label><input type='checkbox' id='$service_id' name='$service_id'";
if($foo['active']) echo " checked='true'";
echo " />";
echo '</div>';

}
}
}

?>
<div class="form-group">
      <div class="col-lg-2 col-lg-offset-1">
        <button type="submit" id="submit" name="submit" value="submit" class="btn btn-primary">Update Active Services</button>
      </div>
</div>

<?=$this->form->end(); ?>

<?php } ?>
