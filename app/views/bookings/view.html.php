<h2 class="title">Bookings</h2>

<?php foreach($bookings as $booking) { ?>

<h2><?=$booking['customer']['name']?> - <?=$booking['service_required']?></h2>

<p><?=$booking['visit_title']?></p>


<?php } ?>
