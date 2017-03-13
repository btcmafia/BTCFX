<div id="error-message-<?=$timeslot_id?>" class="error-slot-message"></div>

<!--
<p class=""><b style="color:#1d175b"><?=$trading_name?></b> is available for <b><span id='slot-<?=$timeslot_id?>'><?=$slots_available?></span> job<?php if($slots_available > 1) echo 's';?></b> at a rate of <b>&pound;<?=$rate?></b>. <span id="<?=$timeslot_id?>" class='assign_text' onclick="decreaseContractorAvailability('<?=$timeslot_id?>')">Assign Job</span>
-->
<p class=""><b style="color:#1d175b"><?=$trading_name?></b> is available for <b><span id='slot-<?=$timeslot_id?>'><?=$slots_available?></span> job<?php if($slots_available > 1) echo 's';?></b> at a rate of <b>&pound;<?=$rate?></b>. <a href="/office/assignvisit/<?=$timeslot_id?>/" class='assign_text'>Assign Job</a></p>
&nbsp;&nbsp;

</p>


