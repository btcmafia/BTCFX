<?php
use lithium\util\String;
use app\extensions\action\Money;
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
      reader.readAsDataURL(f[i]);       }
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


<h2>Withdraw Funds</h2>

<div class="alert alert-dismissible alert-danger col-sm-8 row">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>IMPORTANT:</strong> Like most real banks, taking your money is much easier than giving it back. Unfortunately, we have not yet programmed the withdrawals section yet!
</div>

<div class="col-md-6 row">
        <div class="panel panel-info">
	  <div class="panel-heading">

<ul class="nav nav-tabs">
  <li class="active"><a aria-expanded="true" href="#btc" data-toggle="tab">Bitcoin</a></li>
  <li class=""><a aria-expanded="false" href="#tcp" data-toggle="tab">The Coloured Pound</a></li>
  <li class=""><a aria-expanded="false" href="#dct" data-toggle="tab">Ducat</a></li>
</ul>
          </div>

     <div style="overflow:auto;" class="panel-body">


<div id="myTabContent" class="tab-content">

  <div class="tab-pane fade active in" id="btc">

      <div class="alert alert-dismissible alert-success">
      Your available Bitcoin balance is <strong><?=$balances['BTC']?> BTC</strong>
      </div>                 

	<form action="/in/paymentverify/<?=$currency?>" method="post">

        <label for="currencyaddress">Withdrawal Bitcoin Address</label>
        <div class="input-group">
        	<input type="text" name="currencyaddress" id="currencyaddress" placeholder="Enter recipient's Bitcoin address" class="form-control" title="To Address" data-content="This is the Bitcoin Address of the recipient." value="" onblur="currencyAddress('btc');"/>
                 <span class="input-group-addon"><a href="#" onclick="loadDiv();"><i class="glyphicon glyphicon-qrcode tooltip-x" rel="tooltip-x" data-placement="top" title="Scan using your webcam"></i></a></span></div>

<br />
                <div id="currencyAddressWindow" style="display:none;border:1px solid gray;padding:2px;width:304px;text-align:center ">
                
		<object  id="iembedflash" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="300" height="200">
                <param name="movie" value="/js/qrcode/camcanvas.swf" />
                <param name="quality" value="high" />
                <param name="allowScriptAccess" value="always" />
                <embed  allowScriptAccess="always"  id="embedflash" src="/js/qrcode/camcanvas.swf" quality="high" width="300" height="200" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" mayscript="true"  />
                </object><br />

                <a onclick="captureToCanvas();" class="btn btn-primary">Capture</a>
                <canvas id="qr-canvas" width="300" height="200" style="display:none"></canvas>

                </div><!-- currencyAddressWindow -->

        <?php
             $max = $balances['BTC'];
           ?>
           <?=$this->form->field('amount', array('label'=>'Amount', 'placeholder'=>'0.0', 'class'=>'form-control', 'max'=>$max,'min'=>'0.0','onFocus'=>'SuccessButtonDisable();','maxlength'=>10,'type'=>'number','step'=>'0.00000001')); ?>
          
	<div id="AmountError" style="display:none " class="alert alert-danger">Insufficient Funds</div>
         <input type="hidden" id="maxValue" value="<?=$max?>" name="maxValue">
         <input type="hidden" id="txFee" value="<?=$txfee?>" name="txFee">                                                       <br>
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
                         </table>                         
                </div>
                
          <input type="button" value="Validate" class="btn btn-primary" onclick="return CheckCurrencyPayment('<?=$currency?>');">
          <input type="submit" value="Send" class="btn btn-success" onclick="return CheckCurrencyPayment('<?=$currency?>');" disabled="disabled" id="Send<?=$currency?>SuccessButton">

                    </form>

	<div>
	<?php
if( 0 != count($transactions['BTC']) ) {
?>
<h3>Pending BTC Withdrawals</h3>
        <table class="table table-condensed table-bordered table-hover" style="font-size:11px">
        <thead>
         <tr>
                <th>Date</th><th>Address</th><th>Amount</th><th>Status</th>
        </tr>
        </thead>
        <tbody>
<?php foreach($transactions['BTC'] as $tx) {
$tx['DateTime'] = gmdate('d-M-Y H:i:s',$tx['DateTime']->sec);
 ?>
         <tr>
                <td><?=$tx['DateTime']?></td><td><?=$tx['Address']?></td><td><?=$tx['Amount']?></td><td><?=$tx['Status']?>
	&nbsp;<a title= "Cancel this transaction" href="/in/removetransaction/<?=String::hash($tx['_id'])?>/<?=$tx['_id']?>/withdraw/btc"><i class="fa fa-times"></i></a>
</td>
         </tr>
<?php } ?>
        </tbody>
<?php
}
//
//QUESTION: Why doesn't this function (defined below) work?!!!!

//show_pending_withdrawals($transactions['BTC']); ?>
	</table> 
	</div>
</div>


  <div class="tab-pane fade" id="tcp">
   <h4>Withdraw The Coloured Pound</h4>
   <p>To fund your account with Coloured Pounds or Ducats please send payment to AMyGreatColoredCoinAddress</p> 

   <p>Note: Your TCP and DCT deposit addresses are the same.</p> 
 </div>
  <div class="tab-pane fade" id="dct">
   <h4>Withdraw Ducat</h4>
</div>

</div>

   </div><!-- panel-body -->

	</div><!-- panel -->
</div>	
<?php

function show_pending_withdrawals($transactions) {

if( 0 == count($transactions) ) return;
?>
	<table>
	<thead>
	 <tr>
		<th>Date</th><th>Address</th><th>Amount</th><th>Status</th>
	</tr>
	</thead>
	<tbody>
<?php foreach($transactions as $tx) { ?>
	 <tr>
		<td><?=$tx['DateTime']?></td><td><?=$tx['Address']?></td><td><?=$tx['Amount']?></td><td><?=$tx['Status']?></td>
	 </tr>
<?php } ?>
	</tbody>
<?php
}
?>	
