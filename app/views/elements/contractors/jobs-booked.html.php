<?php
$count = count($jobs);

if($count > 0) {

echo "<h4>Bookings ($count)</h4>";


	foreach($jobs as $job) {

if('unconfirmed' == $job['visit_status']) $class = 'error';
else $class = 'green';


		echo "  <a href='/contractors/viewjob/{$job['visit_id']}' class='jobs-booked-{$job['visit_status']}'>
			{$job['service_required']}: {$job['service_address']} - <span class='$class'>{$job['visit_status']}</span><br />
			</a>";

	}

}
?>
