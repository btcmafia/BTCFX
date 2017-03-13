<h2>Services Provided</h2>

<p><?=COMPANY_NAME?> provide the following plumbing services:</p>

<?php

foreach( $services as $var) {

	echo "<h3>{$var['service_name']}</h3>";

	echo "<p>{$var['service_description']}</p>";

}
?>
