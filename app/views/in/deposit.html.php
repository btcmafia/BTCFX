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

<h2>Deposit Funds</h2>

<?php if(isset($error)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-danger col-sm-4">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>
<?php } ?>

<div class="col-md-6">
        <div class="panel panel-info">
	  <div class="panel-heading">

<div style="float:right;">
<?= $this->_render('element', 'GenerateAddress'); ?>
</div>

<ul class="nav nav-tabs">
  <li class="active"><a aria-expanded="true" href="#btc" data-toggle="tab">Bitcoin</a></li>
  <li class=""><a aria-expanded="false" href="#tcp" data-toggle="tab">TCP &amp; DCT</a></li>
</ul>
          </div>

     <div style="overflow:auto;" class="panel-body">


<div id="myTabContent" class="tab-content">

  <div class="tab-pane fade active in" id="btc">
  
<?php
$type = 'btc_address';
?>
 
	<p>To fund your account with Bitcoin please send payment to <strong><?php echo $default_addresses[$type]; ?></strong></p> 

						<?php   //$qrcode->png($address, QR_OUTPUT_DIR.$address.'.png', 'H', 7, 2);?>
                                        		<table><tr>
					               <td style="text-align:center ;height:280px;vertical-align:middle ">
                                                       <img src="<?=QR_OUTPUT_RELATIVE_DIR.$address?>.png" style="border:1px solid black">
							</td></tr></table>

<div>
<?= $this->_render('element', 'Addresses', compact('addresses', 'type'));?>
</div>

</div><!-- end btc -->
  <div class="tab-pane fade" id="tcp">
<?php
   $type = 'cc_address';
?>
	<p>To fund your account with The Coloured Pound or Ducats please send payment to <strong><?=$default_addresses[$type]?></strong></p> 

						<?php   //$qrcode->png($address, QR_OUTPUT_DIR.$address.'.png', 'H', 7, 2);?>
                                        		<table><tr>
					               <td style="text-align:center ;height:280px;vertical-align:middle ">
                                                       <img src="<?=QR_OUTPUT_RELATIVE_DIR.$address?>.png" style="border:1px solid black">
							</td></tr></table>

   <p>Note: Your TCP and DCT deposit addresses are the same.</p> 
<?= $this->_render('element', 'Addresses', compact('addresses', 'type'));?>
 </div>

</div>

   </div><!-- panel-body -->

	</div><!-- panel -->
</div>		
