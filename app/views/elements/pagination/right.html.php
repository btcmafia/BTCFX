<?php
if($page >= 3) return;

$next_page = $page + 1;
?>
<a href="/<?=$url?>/schedule/<?php echo $next_page; ?>/">
<img src="<?php echo SITE_URL . '/img/right-arrow.png'; ?>" width="25" height="25" />
</a>

