<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2012, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License

 */
 use lithium\storage\Session;
 use app\models\Pages;
 if(!isset($title)){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>'home')
		));
 		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
 }

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="keywords" content="<?php if(isset($keywords)){echo $keywords;} ?>">	
		<meta name="description" content="<?php if(isset($description)){echo $description;} ?>">		
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.ico">


		<title><?php echo MAIN_TITLE;?><?php if(isset($title)){echo $title;} ?></title>

    <!-- Bootstrap core CSS -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/bootstrap/css/dashboard.css?v=<?=rand(1,100000000)?>" rel="stylesheet">
<style type="text/css">
body {
	padding-top: 40px;
	font-family: 'Open Sans', sans-serif;
}
</style>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800italic' rel='stylesheet' type='text/css'>
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="/bootstrap/js/bootstrap-datepicker.js"></script>	
	<?php
	$this->scripts('<script src="/js/main.js?v='.rand(1,100000000).'"></script>'); 	
	?>
</head>
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<?php

if(Session::read('ex')==""){
		Session::write('ex','BTC/GBP');
	}else{
		$ex =	strtoupper(str_replace("_","/",substr($_SERVER['REQUEST_URI'],-7)));
		Session::write('ex',$ex);			
}
$ex = Session::read('ex');

?>
<body <?php if(strtolower($this->_request->controller)=='ex'){ ?> onLoad="UpdateDetails('<?=$ex?>');" <?php }elseif($this->_request->controller!='Sessions' && $this->_request->controller!='Admin'){?> onLoad="CheckServer();" <?php }?>>

    <div style="padding-right:0;" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div  style="padding-right:0; padding-left:0;" class="container-fluid">
						<?php echo $this->_render('element', 'header');?>		
      </div>  <!-- container-fluid -->
    </div> <!-- navbar-fixed-top -->
    <div class="container-fluid">
      <div class="row">
			<?php if(strtolower($this->_request->controller)!='admin'){ ?>
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="#"> <i class="glyphicon glyphicon-th-list"></i> MARKETS</a></li>
						<?php echo $this->_render('element', 'sidebar-menu');?>		
            <li class="active"><a href="#"> <i class="glyphicon glyphicon-th-list"></i> SHARES</a></li>						
						<?php echo $this->_render('element', 'share-menu');?>								
		      </ul>
        </div> <!-- sidebar-->
				<?php }?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
				<?php if(strtolower($this->_request->controller)=='admin'){ ?>
					<?php echo $this->_render('element', 'admin');?>
				<?php }?>
				<?php echo $this->content(); ?>
					<div class="footer">
						<?php echo $this->_render('element', 'footer');?>	
					</div>	<!-- footer -->
				</div> <!-- main -->					
			</div> <!-- row-->
		</div> <!-- container-fluid -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
		<?php echo $this->scripts(); ?>	
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script src="/bootstrap/js/docs.min.js"></script>
  </body>
</html>
<script type="text/javascript">
$(function() {
 $('.tooltip-x').tooltip();
 $('.tooltip-y').tooltip(); 
 $("input:text:visible:first").focus();
});
</script>