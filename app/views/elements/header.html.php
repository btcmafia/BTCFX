<?php
use app\models\Trades;
use lithium\storage\Session;
use app\extensions\action\Functions;
?>
<?php $user = Session::read('member'); ?>
<div class="navbar-header">
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
	<a class="navbar-brand" href="/"><img src="/img/logo.png" alt="IBWT" title="In Bitcoin We Trust"></a>
</div> <!-- navbar-header-->
<div class="navbar-collapse collapse">
	<?php 

	if(strtolower($this->_request->controller)=='ex'){ ?>
		<ul class="nav navbar-nav" style="font-size:12px ">
			<li><a class=" tooltip-x" rel="tooltip-x" data-placement="bottom" title="Latest low price" href="#">Low:<strong><span id="LowPrice" class="btn-success" style="padding:2px"></span></strong></a></li>
			<li><a class=" tooltip-x" rel="tooltip-x" data-placement="bottom" title="Latest high price" href="#">High:<strong><span id="HighPrice" class="btn-danger"  style="padding:2px"></span></strong></a></li>
			<li><a class=" tooltip-x" rel="tooltip-x" data-placement="bottom" title="Latest price" href="#">Last:<strong><span id="LastPrice" class="btn-info"  style="padding:2px"></span></strong></a></li>					
			<li><a class=" tooltip-x" rel="tooltip-x" data-placement="bottom" title="Volume" href="#">Vol:<strong><span id="Volume" class="btn-inverse"  style="padding:2px"></span></strong></a></li>										
		</ul>	
	<?php }else{?>
	<ul class="nav navbar-nav">				
		<li><a>Virtual Currency Exchange</a></li>
	</ul>
<?php }?>				
	<ul class="nav navbar-nav navbar-right">
		<?php if($user!=""){ ?>
			<li ><a href='#' class='dropdown-toggle' data-toggle='dropdown' >
			<?=$user['username']?> <i class='glyphicon glyphicon-chevron-down'></i>
			</a>
			<ul class="dropdown-menu">
				<li><a href="/users/settings">Settings</a></li>			
				<li><a href="/ex/dashboard">Dashboard</a></li>
				<li class="divider"></li>				
<?php 
$trades = Trades::find('all');
$currencies = array();
foreach($trades as $tr){
	$currency = substr($tr['trade'],0,3);
	array_push($currencies,$currency);
	$currency = substr($tr['trade'],4,3);
	array_push($currencies,$currency);
 }	//for

	$currencies = array_unique($currencies);
	foreach($currencies as $currency){
		echo '<li><a href="/users/funding_'.$currency.'">Funding '.$currency.'</a></li>';
	}
?>


				<li class="divider"></li>								
				<li><a href="/users/transactions">Transactions</a></li>							
				<li class="divider"></li>
				<li><a href="/print/">Print / Cold Storage</a></li>											
				<li class="divider"></li>												
				<li><a href="/logout">Logout</a></li>
			</ul>
			<?php }else{?>
			<li><a href="/login">Login / Register</a></li>
			<?php }?>				
		</ul>
</div> <!-- navbar-collapse -->
