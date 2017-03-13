<table class="table table-striped text-center">
<thead>
<tr class="text-center"><th class="text-center">Session</th><th class="text-center">Start</th><th class="text-center">Finish</th><th class="text-center">Minutes</th></tr>
</thead>
<tbody>
<?php foreach($timesheet as $i => $result) { ?>
<tr><td><?=$i?></td><td><?=$result['start']?></td><td><?=$result['finish']?></td><td><?=$result['minutes']?></td></tr>
<?php
$total = $result['minutes'] + $total;
 } ?>
<tr class="success"><td><b>Total</b></td><td>&nbsp;</td><td>&nbsp;</td><td><b><?=$total?></b></td></tr>
</tbody>
</table>
