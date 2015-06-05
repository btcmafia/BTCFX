<?php
use lithium\storage\Session;

?>

<ul class="nav nav-tabs">

<?php 
	$user = Session::read('default');
	if ($user!=""){		
 ?>

 <li class="dropdown"><a aria-expanded="true" class="dropdown-toggle" data-toggle="dropdown" href="#">Trade<span class="caret"></span></a>
<ul class="dropdown-menu">
        <li><a href="/new_trade/x/btc_tcp/">BTC / TCP</a></li>
        <li><a href="/new_trade/x/btc_dct/">BTC / DCT</a></li>
        <li><a href="/new_trade/x/tcp_dct/">TCP / DCT</a></li>
        </ul>
</li>
 <li><a href="/in/accounts/">Account Balances</a></li>
 <li><a href="/in/transactions/">Transactions</a></li>
 <li><a href="/in/orders/">Open Orders</a></li>
 <li><a href="/in/deposit/">Deposit</a></li>
 <li><a href="/in/withdraw/">Withdraw</a></li>
 <li class="dropdown"><a aria-expanded="true" class="dropdown-toggle" data-toggle="dropdown" href="#">Settings<span class="caret"></span></a>
 	<ul class="dropdown-menu">
	<li><a href="/settings/profile/">Account Profile</a></li>
	<li><a href="/settings/security/">Security Settings</a></li>
	</ul>
 </li>

<?php  } else { ?>

<!-- <li><a href="/company/about/">About</a></li>-->
 <li><a href="/login/">Sign In</a></li>
 <li><a href="/register/">Register</a></li>
<!-- <li><a href="/company/faq/">FAQs</a></li>-->

<?php } ?>

 </ul>

