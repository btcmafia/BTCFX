<h2 style="text-align:center;">Contractor Availability
<p><?php echo $this->_render('element', 'forms/services-dropdown', compact('services', 'page', 'service_required')); ?></p>
</h2>

<div class="row">
<div id="office-active-data" class="alert alert-danger col-md-5 col-lg-6 col-lg-offset-3">
<?php if('' != $active_service_address) { ?>
<p><b>Active Customer:</b> <a href="/office/viewcustomer/<?=$active_customer_id?>"><?=$active_customer ?></a>. <?=$active_service_address ?>. [<a href="/office/clearactivecustomer/">Clear</a>]</p>
<?php } else { ?>
<p><b>No Active Customer!</b> Select a customer and service address to assign jobs.</p>
<?php } ?>
</div>
</div>

<div id="select_job_options">
<a href="/office/newcustomer/">Create New Customer</a> or <a href="/office/searchcustomers/">Search Customers</a>.
</div>

<?php 
if(! $service_required) {
?>
<div class="alert alert-info">
Select a service from the dropdown menu above.
</div>
<?php
 }

?>


<?php $url = 'office'; ?>

<div id="pagination">
<?php echo $this->_render('element', 'pagination/left', compact('page', 'url', 'service_required')); ?>
<big>
<?php   if(0 == $page) $day_name = "Today ($day_name)";
        elseif(1 == $page) $day_name = "Tomorrow ($day_name)";
?>
<?=$day_name?>
</big>
<?php echo $this->_render('element', 'pagination/right', compact('page', 'url', 'service_required')); ?>
</div>

<?php


foreach($available_contractors as $period_nicename => $data) {

       if('Morning' == $period_nicename) $period_details = '(9am - 12pm)';
        if('Lunchtime' == $period_nicename) $period_details = '(12pm - 3pm)';
        if('Afternoon' == $period_nicename) $period_details = '(3pm - 6pm)';
        if('Evening' == $period_nicename) $period_details = '(6pm - 9pm)';

?>
<div class="form-group row">
<div class="time-slot-box col-sm-10 col-sm-offset-1">

<h3 class=""><?=$period_nicename?> <small><?=$period_details?></small></h3>

<?php

if('' == $data[0]['trading_name']) echo "<p style='color:red;'>No contractors available.</p>";


foreach($data as $data) {

$trading_name = $data['trading_name'];
$slots_available = $data['slots_available'];
$rate = $data['rate'];
$contractor_id = $data['user_id'];
$timeslot_id = $data['timeslot_id'];

echo $this->_render('element', 'office/slots', compact('trading_name', 'slots_available', 'rate', 'contractor_id', 'timeslot_id'));
}
?>
</div>
</div>
<?php
}
?>
