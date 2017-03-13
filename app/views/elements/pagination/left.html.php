<?php
if($page < 1) return;

$back_page = $page - 1;
$service_required = urlencode($service_required);
?>
<a href="/<?=$url?>/schedule/<?php echo $back_page; ?>/<?php echo $service_required; ?>/"><img src="<?php echo SITE_URL . '/img/left-arrow.png'; ?>" width="25" height="25" /></a>
