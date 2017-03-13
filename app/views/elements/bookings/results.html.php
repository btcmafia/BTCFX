<?php foreach($bookings as $booking) { ?>

<div class="panel panel-default">

  <div class="panel-heading">
<h4><a href='/<?=$context?>/viewjob/<?=$booking['_id']?>/'><?=$booking['visit_title']?></a></h4>
<p><?=$booking['time_and_date']['date']?> - <?=$booking['time_and_date']['period']?></p>
 </div>

<div class="panel-body">
<?php if($is_staff) { ?><p>Customer: <a href="/office/viewcustomer/<?=$booking['customer_id']?>/"><?=$booking['customer']['name']?></a><br />
			   Contractor: <a href="/bookings/contractor/<?=$booking['contractor_id']?>/"><?=$booking['contractor_name']?></a></p> <?php } ?>
<p><?php echo $booking['service_address']['address']; ?></p>
<p><?php echo $booking['service_address']['contact_name']; ?> - <?php echo $booking['service_address']['phone']; ?></p>
</div>

</div>
<?php } ?>
