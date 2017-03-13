<?php
use lithium\storage\Session;

?>

<ul class="nav nav-tabs">

<?php 
	$user = Session::read('default');

//print_r($user);
if($is_staff == true) {

?>
 <li><a href="/activity/office/all/">Activity</a></li>

	<li role="presentation" class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
      Bookings <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
      <li><a href="/bookings/office/all/">All Bookings</a></li>
      <li><a href="/bookings/office/pending/">Customer Pending</a></li>
      <li><a href="/bookings/office/unconfirmed/">Contractor Pending</a></li>
      <li><a href="/bookings/office/confirmed/">Booked / Confirmed</a></li>
      <li><a href="/bookings/office/ongoing/">In Progress</a></li>
      <li><a href="/bookings/office/paused/">Paused</a></li>
      <li><a href="/bookings/office/completed/">Completed</a></li>
      <li><a href="/bookings/office/rebook/">New Booking Req.</a></li>
      <li><a href="/bookings/office/closed/">Closed</a></li>
    </ul>
  </li>

 <li><a href="/office/schedule/">Schedule</a></li>


<?php
	} elseif ($is_contractor){		

 ?>

<!-- <li><a href="/contractors/faqs/">About</a></li> -->
 <li><a href="/contractors/services/">Services</a></li>
 <li><a href="/bookings/contractor/">Bookings</a></li>
 <li><a href="/contractors/schedule/">Schedule</a></li>

<?php } elseif($is_admin) { ?>

 <li><a href="/admin/newcontractor/">Add Contractor</a></li>
 <li><a href="/admin/contractorlist/">List Contractors</a></li>

<?php  } else { ?>

 <li><a href="/login/">Sign In</a></li>
 <li><a href="/register/">Register</a></li>

<?php } ?>

 </ul>

