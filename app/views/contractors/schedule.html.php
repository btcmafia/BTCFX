<?php
?>
<h2 id="mobile-title">My Availability</h2>

<div class="col-sm-11">

<?php

if( 'no' == $services_active ) {
$error = "You haven't activated any services yet. Please visit the services page first.";
?>
<div class="alert alert-dismissible alert-danger">
  <strong>Error!</strong> <?php echo $error; ?>
</div>
<?php
return;
}
?>


<div class="form-group row"><?php //echo $this->_render('element', 'contractors/ready', compact('ready_status')); ?></div>

<div id="error-message"></div>
<?php $url = 'contractors'; ?>

<div id="pagination">
<?php echo $this->_render('element', 'pagination/left', compact('page', 'url')); ?>
<big>
<?php   if(0 == $page) $day_name = "Today ($day_name)";
	elseif(1 == $page) $day_name = "Tomorrow ($day_name)";
?>
<?=$day_name?></big>
<?php echo $this->_render('element', 'pagination/right', compact('page', 'url')); ?>
</div>

<?php
//$periods = array('Morning <small>(9am - 12pm)</small>', 'Lunchtime <small>(12pm - 3pm)</small>', 'Afternoon <small>(3pm - 6pm)</small>');

foreach($periods as $period) {

$timeslot_id = $period['timeslot_id'];
$period_nicename = $period['period_nicename'];
$period_details = $period['period_details'];
$slots_available = $period['slots_available'];
$jobs = $period['jobs_booked'];
$order_id = $period['order'];
//print_r("<h2>$jobs</h2>");
?>



<div class="form-group row"><?php echo $this->_render('element', 'contractors/slots', compact('timeslot_id', 'period_nicename', 'period_details', 'slots_available', 'jobs', 'order_id')); ?></div>

<?php
 } //foreach
?>
</div>
