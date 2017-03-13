<?php
//$slots_available = '3';
?>

<div class="time-slot-box col-sm-10 col-sm-offset-1">

<h3 class=""><?=$period_nicename?> <small><?=$period_details?></small></h3>

<div id="error-message-<?=$timeslot_id?>" class="error-slot-message"></div>

<p class=""><big>I'm available for <b><span id="slot-<?=$timeslot_id?>"><?=$slots_available?></span></b> jobs</big>
&nbsp;&nbsp;

<img src="/img/up-arrow.png" alt="Increase" onclick="increaseAvailability('<?=$timeslot_id?>')" />
<img src="/img/down-arrow.png" alt="Decrease" onclick="decreaseAvailability('<?=$timeslot_id?>')" />
</p>
<div id="contractor-slots-jobs-booked">
<?php
echo $this->_render('element', 'contractors/jobs-booked', compact('jobs'));
?>
</div>

</div>
