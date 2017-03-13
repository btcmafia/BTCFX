<h2>Edit Services Allowed for <?=$trading_name?></h2>

<p>Do not allow contractors to do any jobs which require qualifications without a copy on file!</p>

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


<?php

echo $this->form->create('', array('class' => 'form-horizontal', 'name' => 'update-contractor-allowed-services'));

foreach($trades as $trade) {

echo "<h3>$trade</h3>";

foreach($service_categories as $category) {

echo "<caption>$category</caption>";

foreach($services[$trade][$category] as $foo) {

/*
echo '<blockquote>';
print_r($foo);
echo '</blockquote>';
*/

$service_id = $foo['service_id'];
$service_name = $foo['service_name'];


echo '<div class="form-group">';
echo "<label for='$service_id' class='col-lg-2 control-label'>$service_name</label><input type='checkbox' id='$service_id' name='$service_id'";
if($foo['allowed']) echo " checked='true'";
echo " />";
echo '</div>';
}
}
}
?>
<div class="form-group">
      <div class="col-lg-2 col-lg-offset-1">
        <button type="submit" id="submit" name="submit" value="submit" class="btn btn-primary">Update Allowed Services</button>
      </div>
</div>

<?=$this->form->end(); ?>


