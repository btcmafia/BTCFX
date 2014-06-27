<?php
use app\models\Details;
	$count = Details::find('count',array(
		'conditions' => array(
			'company'=>array('$exists'=>true),
			'company.verified'=>'Yes')
	));
$details = Details::find('all',array(
		'conditions' => array(
			'company'=>array('$exists'=>true),
			'company.verified'=>'Yes')
	));
?>
<div id="sidebar"> 
	<a href="#Shares" class="list-group-item item-border" data-toggle="collapse" data-parent="#sidebar">
		<i class="glyphicon glyphicon-plus"></i> Companies <span class="badge bg_danger"><?=$count?></span>
	</a>
	<div id="Shares" class="list-group subitem collapse">
	<?php foreach($details as $detail){?>
		<a href="/shares/x/btc_<?=strtolower($detail['company']['ShortName'])?>" class="list-group-subitem">
			<i class="glyphicon glyphicon-caret-right"></i><?=$detail['company']['ShortName']?> <span class="badge btn-success"><?=number_format($detail['company']['price'][0],2)?></span>
		</a>				
	<?php }?>
		</div> <!-- subitem -->
</div> <!-- sidebar -->           
