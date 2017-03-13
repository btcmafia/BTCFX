<div id="manage-booking-controls">
<?php if(0 == $job['status']['code']) { // pending customer approval ?>

<h1>Pending Job</h1>

<div class="row">
<div class="alert alert-danger col-sm-6">
<p>The customer has not yet confirmed this booking. No action can currently be taken.</p>
</div>
</div>


<?php } elseif(1 == $job['status']['code']) { // pending contractor acceptance ?>

<h1>Accept Job</h1>

<div class="row button"><a href="/contractors/acceptbooking/<?=$job['_id']?>"><img src="/img/accept.png"/></a></div>
<div class="row button"><a href="/contractors/rejectbooking/<?=$job['_id']?>"><img src="/img/reject.png"/></a></div>

<?php } elseif(2 == $job['status']['code']) { // accepted by both ?>

<h1>Manage Booking</h1>

<div class="row button"><a href="/contractors/onmyway/<?=$job['_id']?>"><img src="/img/onmyway.png"/></a></div>
<div class="row button"><a href="/contractors/startvisit/<?=$job['_id']?>"><img src="/img/start.png"/></a></div>
<div class="row button"><a href="/contractors/rejectbooking/<?=$job['_id']?>"><img src="/img/reject.png"/></a></div>

<?php } elseif(4 == $job['status']['code']) { // contractor on the way ?>

<h1>Manage Booking</h1>

<div class="row button job-status ontheway">You are currently on your way to this job!</div>
<div class="row button"><a href="/contractors/startvisit/<?=$job['_id']?>"><img src="/img/start.png"/></a></div>
<div class="row button"><a href="/contractors/rejectbooking/<?=$job['_id']?>"><img src="/img/reject.png"/></a></div>

<?php } elseif(5 == $job['status']['code']) { //started ?>

<h1>Manage Booking</h1>

<div class="row button job-status started">You are currently working on this job!</div>
<div class="row button"><a href="/contractors/pausevisit/<?=$job['_id']?>"><img src="/img/pause.png"/></a></div>
<div class="row button"><a href="/contractors/jobreport/<?=$job['_id']?>/"><img src="/img/completed.png"/></a></div>

<?php } elseif(6 == $job['status']['code']) { //paused ?>

<h1>Manage Booking</h1>

<div class="row job-status paused">This job is currently paused!</div>
<div class="row button"><a href="/contractors/startvisit/<?=$job['_id']?>"><img src="/img/start.png"/></a></div>
<div class="row button"><a href="/contractors/jobreport/<?=$job['_id']?>/"><img src="/img/completed.png"/></a></div>

<?php } ?>

</div>

<div class="center-block text-left">

<?php
$map_location = urlencode($job['service_address']['address']);
?>
<p><span class="label">Time &amp; Date: </span> <?=$job['time_and_date']['nicename']?></p>
<p><span class="label">Address:</span> <a href='https://maps.google.com?q=<?=$map_location?>'><?=$job['service_address']['address']?></a></p>
<p><span class="label">Service Category:</span> <?=$job['service_required']?></p>
<p><span class="label">Job Description:</span> <?=$job['visit_title']?></p>

<?php
if('' != $job['service_address']['phone']) $phone = " - <a href='tel:{$job['service_address']['phone']}'>{$job['service_address']['phone']}</a>";
else $phone = '';
?>

<p><span class="label">Contact Details: </span> <?=$job['service_address']['contact_name']?> <?php echo $phone; ?>

</div>
