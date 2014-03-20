<?php
use app\models\Parameters;
use lithium\core\Environment; 
$Comm = Parameters::find('first');
?>
<h2>Welcome to IBWT</h2>
	<p>First time on this website, no problem follow these simple steps to get your self trading on IBWT</p>
	<div class="row">
			<div id="one" class="col-md-4 boxes">
					<h3>Register Account</h3>
					<p>Easy and simple registration, experience the IBWT platform with only an email address to get started.</p>
			</div>
			<div id="two" class="col-md-4 col-sm-4 boxes">
					<h3>Buy and Sell</h3>
					<p>Choose from a multi-tued of cash and coins for your day to day trades or shopping needs.</p>
			</div>
			<div id="three"  class="col-md-4 col-sm-4 boxes">
					<h3>Deposit or Withdraw</h3>
					<p>Withrdraw your coins straight away or have cash sent to you with simple step verfication.</p>
			</div>
	</div>
			
			<div class="row">
					<div class="col-xs-12 col-sm-8">
							<h4 class="iconArticle" >IBWT is a Bitcoin/Virtual Currency exchange, offering a fully regulated, secure method, for individuals and businesses to buy or sell bitcoins.</h4>
							<ul class="home-bullets">
							 <li>Fees are <strong><?=$Comm['value']?>%</strong> per transaction</li>
							 <li>Simple verification means you could be a full customer in a matter of days</li>
							 <li>Security ensured with Cold Storage, SSL 256bit encryption & 2FA</li>
							 <li>Dedicated Server for an enhanced customer experience</li>
							 <li>Deposits via OKPAY or secure mail services.</li>
							 <li>Withdrawal via OKPAY, secure mail services or via banks.</li>
							</ul>
				</div>
				<div class="col-xs-12 col-sm-4">
				<a class="twitter-timeline"  href="https://twitter.com/IBWTofficial"  data-widget-id="439157793853947904">Tweets by @IBWTofficial</a>
				</div>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>