<?php use lithium\core\Environment; 
if(substr(Environment::get('locale'),0,2)=="en"){$locale = "en";}else{$locale = Environment::get('locale');}
?>
<?php
$howmany = 100;
use app\models\Trades;
$trades = Trades::find('all',array('limit'=>$howmany));
$tradesall = Trades::find('all');
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
?>
<ul class="nav" aria-labelledby="dropdown<?=$currency?>" data-toggle="dropdown">
	  <li class="dropdown" style="border-bottom:1px solid gray ">
		<a href="" data-toggle="dropdown" ><?=$currency?>
		<span class="caret"></span>
		</a>
		<ul class="dropdown-menu"><?php 
		$tradescurrencies = Trades::find('all',array(
			'conditions'=>array('trade'=>array('$regex'=>$currency)),
		));
		foreach ($tradescurrencies as $tc){?>
			<li><a href="/ex/x/<?=strtolower(str_replace("/","_",$tc['trade']))?>"><?=$tc['trade']?></a></li>
<?php }	//for?>			
		</ul>
	</li>
<?php }	//for?>
</ul>
<?php }	//if?>
<p><a href="https://ibwt.co.uk/ex/x/btc_gbp">this</a></p>