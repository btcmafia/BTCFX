<?php
use lithium\util\String;
use li3_qrcode\extensions\action\QRcode;
	$qrcode = new QRcode();
?>
<script type="text/javascript" src="/js/qrcode/grid.js"></script>
<script type="text/javascript" src="/js/qrcode/version.js"></script>
<script type="text/javascript" src="/js/qrcode/detector.js"></script>
<script type="text/javascript" src="/js/qrcode/formatinf.js"></script>
<script type="text/javascript" src="/js/qrcode/errorlevel.js"></script>
<script type="text/javascript" src="/js/qrcode/bitmat.js"></script>
<script type="text/javascript" src="/js/qrcode/datablock.js"></script>
<script type="text/javascript" src="/js/qrcode/bmparser.js"></script>
<script type="text/javascript" src="/js/qrcode/datamask.js"></script>
<script type="text/javascript" src="/js/qrcode/rsdecoder.js"></script>
<script type="text/javascript" src="/js/qrcode/gf256poly.js"></script>
<script type="text/javascript" src="/js/qrcode/gf256.js"></script>
<script type="text/javascript" src="/js/qrcode/decoder.js"></script>
<script type="text/javascript" src="/js/qrcode/qrcode.js"></script>
<script type="text/javascript" src="/js/qrcode/findpat.js"></script>
<script type="text/javascript" src="/js/qrcode/alignpat.js"></script>
<script type="text/javascript" src="/js/qrcode/databr.js"></script>
<style>
.Address_success{background-color: #9FFF9F;font-weight:bold}
</style>
<script type="text/javascript">
var gCtx = null;
	var gCanvas = null;

	var imageData = null;
	var ii=0;
	var jj=0;
	var c=0;
	
	
function dragenter(e) {
  e.stopPropagation();
  e.preventDefault();
}

function dragover(e) {
  e.stopPropagation();
  e.preventDefault();
}
function drop(e) {
  e.stopPropagation();
  e.preventDefault();

  var dt = e.dataTransfer;
  var files = dt.files;

  handleFiles(files);
}

function handleFiles(f)
{
	var o=[];
	for(var i =0;i<f.length;i++)
	{
	  var reader = new FileReader();

      reader.onload = (function(theFile) {
        return function(e) {
          qrcode.decode(e.target.result);
        };
      })(f[i]);

      // Read in the image file as a data URL.
      reader.readAsDataURL(f[i]);	}
}
	
function read(a)
{
 $("#currencyaddress").val(a);
 $("#SendAddress").html(a); 
 $("#currencyaddress").addClass("Address_success");
 $("#currencyAddressWindow").hide();
}	
	
function loadDiv()
{
	$("#currencyAddressWindow").show();
	initCanvas(300,200);
	qrcode.callback = read;
	qrcode.decode("");
}

function initCanvas(ww,hh)
	{
		gCanvas = document.getElementById("qr-canvas");
		gCanvas.addEventListener("dragenter", dragenter, false);  
		gCanvas.addEventListener("dragover", dragover, false);  
		gCanvas.addEventListener("drop", drop, false);
		var w = ww;
		var h = hh;
		gCanvas.style.width = w + "px";
		gCanvas.style.height = h + "px";
		gCanvas.width = w;
		gCanvas.height = h;
		gCtx = gCanvas.getContext("2d");
		gCtx.clearRect(0, 0, w, h);
		imageData = gCtx.getImageData( 0,0,320,240);
	}

	function passLine(stringPixels) { 
		//a = (intVal >> 24) & 0xff;

		var coll = stringPixels.split("-");
	
		for(var i=0;i<320;i++) { 
			var intVal = parseInt(coll[i]);
			r = (intVal >> 16) & 0xff;
			g = (intVal >> 8) & 0xff;
			b = (intVal ) & 0xff;
			imageData.data[c+0]=r;
			imageData.data[c+1]=g;
			imageData.data[c+2]=b;
			imageData.data[c+3]=255;
			c+=4;
		} 

		if(c>=320*240*4) { 
			c=0;
      			gCtx.putImageData(imageData, 0,0);
		} 
 	} 

 function captureToCanvas() {
		flash = document.getElementById("embedflash");
		flash.ccCapture();
		qrcode.decode();

 }

</script>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Funding <?=$currencyName?> - <?=$currency?> - Deposit / Withdrawal</h3>
  </div>
  <div class="panel-body">
		<div class="row">
		<!-- Deposit -->
			<div class="col-md-6">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Deposit <?=$currencyName?> - <?=$currency?></h3>
					</div>
					<div class="panel-body">
							<table class="table table-condensed table-bordered table-hover">
								<tr style="background-color:#CFFDB9">
									<td><?=$currencyName?> - <?=$currency?> Address</td>
								</tr>
								<tr>
									<td>To add <?=$currencyName?> please send payment to: <strong><?=$address?></strong></td>
								</tr>
								<tr>
								<?php	$qrcode->png($address, QR_OUTPUT_DIR.$address.'.png', 'H', 7, 2);?>
									<td style="text-align:center ;height:280px;vertical-align:middle ">
										<img src="<?=QR_OUTPUT_RELATIVE_DIR.$address?>.png" style="border:1px solid black">
									</td>
								</tr>
							</table>
					
					</div>
				</div>				
			<?php if($currency=="MSC"){?>
			Use MasterCoin client to send your MasterCoins to the above address using:
			<code>send_MP(senderaddress, <?=$address?>,<br>
			1, amount);</code><br>
			Where 1 is MasterCoin propertyID as<br>
			<code>{<br>
    "name" : "MasterCoin",<br>
    "category" : "N/A",<br>
    "subcategory" : "N/A",<br>
    "data" : "***data***",<br>
    "url" : "www.mastercoin.org",<br>
    "divisible" : true,<br>
    "issuer" : "1EXoDusjGwvnjZUyKkxZ4UHEf77z6A5S4P",<br>
    "creationtxid" : "0000000000000000000000000000000000000000000000000000000000000000",<br>
    "fixedissuance" : false,<br>
    "totaltokens" : 0.00000000<br>
}<br>
</code>
			<?php }?>
			</div>		
			
			
		<!-- Deposit -->
		<!-- Withdraw -->
			<div class="col-md-6">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Withdraw <?=$currencyName?> - <?=$currency?></h3>
					</div>
					<div class="panel-body">
					
					<?php 
					if(count($transactions)==0){
					?>
							<table class="table table-condensed table-bordered table-hover">
								<tr style="background-color: #FEECE0">
									<td><?=$currencyName?> - <?=$currency?> balance</td>
								</tr>
								<tr>
									<td><strong><?=number_format($details['balance.'.$currency],8)?> <?=$currency?></strong><br><br></td>
								</tr>
								<tr>
									<td style="height:280px ">
										<form action="/users/paymentverify/<?=$currency?>" method="post">

										<label for="currencyaddress"><?=$currencyName?> - <?=$currency?> Address</label>
									<div class="input-group">										
				<input type="text" name="currencyaddress" id="currencyaddress" placeholder="15AXfnf7hshkwgzA8UKvSyjpQdtz34H9LE" class="form-control" title="To Address" data-content="This is the <?=$currencyName?> - <?=$currency?> Address of the recipient." value="" onblur="currencyAddress('<?=$currency?>');"/>
									<span class="input-group-addon"><a href="#" onclick="loadDiv();"><i class="glyphicon glyphicon-qrcode tooltip-x" rel="tooltip-x" data-placement="top" title="Scan using your webcam"></i></a></span></div>


									<small class="help-block">Enter The <?=$currencyName?> - <?=$currency?> Address of the Recipient</small>
				
									<div id="currencyAddressWindow" style="display:none;border:1px solid gray;padding:2px;width:304px;text-align:center ">
									<object  id="iembedflash" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="300" height="200">
									<param name="movie" value="/js/qrcode/camcanvas.swf" />
									<param name="quality" value="high" />
									<param name="allowScriptAccess" value="always" />
									<embed  allowScriptAccess="always"  id="embedflash" src="/js/qrcode/camcanvas.swf" quality="high" width="300" height="200" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" mayscript="true"  />
									</object><br>
									<a onclick="captureToCanvas();" class="btn btn-primary">Capture</a>
									<canvas id="qr-canvas" width="300" height="200" style="display:none"></canvas>
									</div>
				
									<?php
									$max = (float)$details['balance.'.$currency];
									?>
											<?=$this->form->field('amount', array('label'=>'Amount', 'placeholder'=>'0.0', 'class'=>'form-control', 'max'=>$max,'min'=>'0.001','onFocus'=>'SuccessButtonDisable();','maxlenght'=>10)); ?>
											<div id="AmountError" style="display:none " class="alert alert-danger">Amount incorrect!</div>
											<input type="hidden" id="maxValue" value="<?=$max?>" name="maxValue">
											<input type="hidden" id="txFee" value="<?=$txfee?>" name="txFee">							<br>
											<input type="hidden" id="TransferAmount" value="0" name="TransferAmount" onFocus="SuccessButtonDisable()">											
											
											<div class="alert alert-warning" id="<?=$currency?>Alert" style="display:none"></div>
											<div id="SendCalculations">
												<table class="table table-condensed table-bordered table-hover">
													<tr>
														<th width="30%">Send to:</th>
														<td id="Send<?=$currency?>Address"></td>
													</tr>
													<tr>
														<th>Total Amount:</th>
														<td id="Send<?=$currency?>Amount"></td>
													</tr>
													<tr>
														<th>Transaction Fees:<br>
														<small>to miners</small></th>
														<td id="Send<?=$currency?>Fees"></td>
													</tr>
													<tr>
														<th>Amount You Receive:</th>
														<th id="Send<?=$currency?>Total"></th>
													</tr>
												</table>
											</div>
											<input type="button" value="Calculate" class="btn btn-primary" onclick="return CheckCurrencyPayment('<?=$currency?>');">
											<input type="submit" value="Send" class="btn btn-success" onclick="return CheckCurrencyPayment('<?=$currency?>');" disabled="disabled" id="Send<?=$currency?>SuccessButton"> 
											
										</form>
									</td>
								</tr>
							</table>
							<?php }else{?>
							<table class="table table-condensed table-bordered table-hover">
								<tr style="background-color:#CFFDB9">
									<td>Withdrawal request</td>
								</tr>
								<tr>
									<td style="height:325px ">
									You have already made a withdrawal request for <strong><?=number_format($transactions['Amount'],8)?> <?=$transactions['Currency']?></strong> . Please check your email and complete the request. If you want to cancel the request, please send an email to <a href="mailto:support@ibwt.co.uk" >support@ibwt.co.uk</a>
									If your want to delete this request yourself, you can click on the link below:
									</td>
								</tr>
								<tr>
									<td>
										<strong><a href="/Users/removetransaction/<?=String::hash($transactions['_id'])?>/<?=$transactions['_id']?>/funding/<?=$transactions['Currency']?>">REMOVE <i class="fa fa-remove"></i> <?=number_format($transactions['Amount'],8)?> <?=$transactions['Currency']?></a></strong>
									</td>
								</tr>
							</table>
							<?php }?>
					</div>
				</div>				
				
				<?php if($currency=="MSC"){?>
			We will send your MasterCoins to the above address using:
			<code>send_MP(senderaddress, addressw,<br>
			1, amount);</code><br>
			Where 1 is MasterCoin propertyID as<br>
			<code>{<br>
    "name" : "MasterCoin",<br>
    "category" : "N/A",<br>
    "subcategory" : "N/A",<br>
    "data" : "***data***",<br>
    "url" : "www.mastercoin.org",<br>
    "divisible" : true,<br>
    "issuer" : "1EXoDusjGwvnjZUyKkxZ4UHEf77z6A5S4P",<br>
    "creationtxid" : "0000000000000000000000000000000000000000000000000000000000000000",<br>
    "fixedissuance" : false,<br>
    "totaltokens" : 0.00000000<br>
}<br>
</code>
			<?php }?>
				
			</div>		
		<!-- Withdraw -->					
		</div>
  </div>
	<div class="panel-footer"></div>
