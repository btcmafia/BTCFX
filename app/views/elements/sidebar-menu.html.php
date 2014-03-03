<?php use lithium\core\Environment; 
if(substr(Environment::get('locale'),0,2)=="en"){$locale = "en";}else{$locale = Environment::get('locale');}
?>
<?php
$howmany = 100;
use app\models\Trades;
use app\models\Orders;
use lithium\data\Connections;

$trades = Trades::find('all',array('limit'=>$howmany));
$tradesall = Trades::find('all');


		$mongodb = Connections::get('default')->connection;
		$Rates = Orders::connection()->connection->command(array(
			'aggregate' => 'orders',
			'pipeline' => array( 
				array( 
				'$project' => array(
					'_id'=>0,
					'Action' => '$Action',
					'PerPrice'=>'$PerPrice',					
					'Completed'=>'$Completed',					
					'FirstCurrency'=>'$FirstCurrency',
					'SecondCurrency'=>'$SecondCurrency',	
					'TransactDateTime' => '$Transact.DateTime',
				)),
				array('$match'=>array(
					'Completed'=>'Y',					
					)),
				array('$group' => array( '_id' => array(
							'FirstCurrency'=>'$FirstCurrency',
							'SecondCurrency'=>'$SecondCurrency',	
							'year'=>array('$year' => '$TransactDateTime'),
							'month'=>array('$month' => '$TransactDateTime'),						
							'day'=>array('$dayOfMonth' => '$TransactDateTime'),												
//						'hour'=>array('$hour' => '$TransactDateTime'),
						),
					'min' => array('$min' => '$PerPrice'), 
					'avg' => array('$avg' => '$PerPrice'), 					
					'max' => array('$max' => '$PerPrice'), 
				)),
				array('$sort'=>array(
					'_id.year'=>-1,
					'_id.month'=>-1,
					'_id.day'=>-1,					
//					'_id.hour'=>-1,					
				)),
				array('$limit'=>1)
			)
		));

$sel_curr = $this->_request->params['args'][0];
if($this->_request->params['controller']!='api'){
	$currencies = array();
	foreach($trades as $tr){
		$currency = substr($tr['trade'],0,3);

		array_push($currencies,$currency);
		$currency = substr($tr['trade'],4,3);

		array_push($currencies,$currency);
	 }	//for
	$currencies = array_unique($currencies);
	?>
	<?php
	foreach($currencies as $currency){
		$exchanges = Trades::find('all',array(
			'conditions'=>array('trade'=>array('$regex'=>$currency))
		));
		$count = count($exchanges);
?>
    <div id="sidebar"> 
		<a href="#<?=$currency?>" class="list-group-item item-border" data-toggle="collapse" data-parent="#sidebar"><i class="glyphicon glyphicon-plus"></i> <?=$currency?><span class="badge bg_danger"><?=$count?></span></a>
			<div id="<?=$currency?>" class="list-group subitem collapse">	
				<?php foreach($exchanges as $exchange){
						$first_currency = substr($exchange['trade'],0,3);		
						$second_currency = substr($exchange['trade'],4,3);		
						$avg = 0;
				?>
				<?php foreach($Rates['result'] as $rate){?>
					<?php if($rate['_id']['FirstCurrency']==$first_currency && $rate['_id']['SecondCurrency']==$second_currency){?>
					<?php	$avg = $rate['avg'];?>
					<?php }?>				
				<?php }?>
				
				<a href="/ex/x/<?=strtolower(str_replace("/","_",$exchange['trade']))?>" class="list-group-subitem"><i class="glyphicon glyphicon-caret-right"></i> 
				<?=$exchange['trade']?>
				<span class="badge btn-success"><?=number_format($avg,2)?></span></a>				
				<?php }?>
			</div>
<?php }	//for?>
<?php }	//if?>
