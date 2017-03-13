<?php
use app\models\Trades;
use lithium\storage\Session;
use app\extensions\action\Functions;
?>
<div class="navbar-header">
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>


<h1 class="col-sm-8" id="site-title"><nobr><a href='<?php echo SITE_URL; ?>'><?php echo COMPANY_NAME; ?></a></nobr></h1>

</div> <!-- navbar-header-->
<div class="navbar-collapse collapse">
	<?php 
			if(strtolower($this->_request->controller)=='ex'){ ?>
	
	<?php }else{?>
	
<?php }?>				
	<ul class="nav navbar-nav navbar-right">
		<?php if($username!=""){ ?>
			<li ><a href='#' class='dropdown-toggle' data-toggle='dropdown' >
			<?=$username?> <i class='glyphicon glyphicon-chevron-down'></i>&nbsp;&nbsp;&nbsp;
			</a>
			<ul class="dropdown-menu">
				<li><a href="/contractors/faqs">FAQs</a></li>
				<li><a href="/contractors/settings">Settings</a></li>
				<li><a href="/logout"><i class="fa fa-power-off"></i> Logout</a></li>
			</ul>
			<?php }else{?>
			<li ><a href='#' class='dropdown-toggle' data-toggle='dropdown'>
			Login / Register
			<i class='glyphicon glyphicon-chevron-down'></i>&nbsp;&nbsp;&nbsp;</a>
			<ul class="dropdown-menu">
				<li><a href="/login">Login</a></li>
				<li><a href="/register">Register</a></li>
			</ul>
			<?php }?>				
		</ul>
</div> <!-- navbar-collapse -->

