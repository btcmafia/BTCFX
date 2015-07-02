<?php
use lithium\util\String;
?>
<div class="row"?

<h4>Withdraw <?=$currency_long?></h4>

     <div class="alert alert-dismissible alert-success">
      Your available <?=$currency_long?> balance is <strong><?=$balances[$currency]?> <?=$currency?></strong>
      </div>

        <form action="" method="post">

        <label for="CurrencyAddress<?=$currency?>">Withdrawal <?=$currency_long?> Address</label>
        <div class="input-group">
                <input type="text" name="CurrencyAddress<?=$currency?>" id="CurrencyAddress<?=$currency?>" placeholder="Enter recipient's <?=$currency_long?> address" class="form-control" title="To Address" data-content="This is the <?=$currency_long?> Address of the recipient." value="" onblur="currencyAddress(<?=$currency?>);"/>
                 <span class="input-group-addon"><a href="#" onclick="loadDivBTC();"><i class="glyphicon glyphicon-qrcode tooltip-x" rel="tooltip-x" data-placement="left" title="Scan using your webcam"></i></a></span></div>

<br />
		<!-- CurrencyAddressWindow -->
                <div id="<?=$currency?>AddressWindow" style="display:none;border:1px solid gray;padding:2px;width:304px;text-align:center ">

                <object  id="iembedflash" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="300" height="200">
                <param name="movie" value="/js/qrcode/camcanvas.swf" />
                <param name="quality" value="high" />
                <param name="allowScriptAccess" value="always" />
                <embed  allowScriptAccess="always"  id="embedflash" src="/js/qrcode/camcanvas.swf" quality="high" width="300" height="200" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" mayscript="true"  />
                </object><br />

                <a onclick="captureToCanvas();" class="btn btn-primary">Capture</a>
                <canvas id="qr-canvas" width="300" height="200" style="display:none"></canvas>

                </div><!-- end CurrencyAddressWindow -->

           <?=$this->form->field("Amount$currency", array('label'=>"Amount$cuurency", 'id'=>"Amount$currency", 'placeholder'=>'0.0', 'class'=>'form-control', 'max'=>$max,'min'=>'0.0','onFocus'=>'SuccessButtonDisable();','maxlength'=>10,'type'=>'number','step'=>'0.01')); ?>

        <div id="AmountError<?=$currency?>" style="display:none " class="alert alert-danger">Insufficient Funds</div>
         <input type="hidden" id="MaxValue<?=$currency?>" value="<?=$balances[$currency]?>" name="maxValue">
         <input type="hidden" id="TransferAmount<?=$currency?>" value="0" name="TransferAmount" onFocus="SuccessButtonDisable()">
         <input type="hidden" id="currency" value="<?=$currency?>" name="currency" />

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

          <input type="button" value="Validate" class="btn btn-primary" onclick="return CheckCurrencyPayment(<?=$currency?>);">
          <input type="submit" value="Send" class="btn btn-success" onclick="return CheckCurrencyPayment(<?=$currency?>);" id="Send<?=$currency?>SuccessButton">

                    </form>

        <?php
if( 0 != count($transactions[$currency]) ) {
?>
        <div>
<h3>Pending <?=$currency?> Withdrawals</h3>
        <table class="table table-condensed table-bordered table-hover" style="font-size:11px">
        <thead>
         <tr>
                <th>Date</th><th>Address</th><th>Amount</th><th>Status</th>
        </tr>
        </thead>
        <tbody>
<?php foreach($transactions[$currency] as $tx) {
$tx['DateTime'] = gmdate('d-M-Y H:i:s',$tx['DateTime']->sec);
 ?>
         <tr>
                <td><?=$tx['DateTime']?></td><td><?=$tx['Address']?></td><td><?=$tx['Amount']?></td><td><?=$tx['Status']?>
        &nbsp;<a title= "Cancel this transaction" href="/in/removetransaction/<?=String::hash($tx['_id'])?>/<?=$tx['_id']?>/withdraw/btc"><i class="fa fa-times"></i></a>
</td>
         </tr>
<?php } ?>
        </tbody>
     </table>
        </div>
<?php
}
?>
</div>

