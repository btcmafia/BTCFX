<h2>Active Contractors</h2>

<?php

$site_url = SITE_URL;

foreach($contractors as $foo) {

echo "<a href='/admin/contractorservices/{$foo['user_id']}'>{$foo['trading_name']}</a><br />";
}

?>
