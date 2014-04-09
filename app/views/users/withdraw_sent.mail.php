<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="https://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.gif" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi <?=$transaction['username']?>,</h4>
<p>Your <strong><?=abs($transaction['Amount'])?> <?=$currency?></strong> is sent to <?=$transaction['address']?> from <?=COMPANY_URL?>.</p>
<?php
		///////////////////// Change of code required when Virtual Currency added
			switch($currency){
					case "XGC":
					$url = "http://greencoin.io/blockchain/tx/".$txid;					
					break;
					case "BTC":
					$url = "http://blockchain.info/tx/".$txid;
					break;
					case "LTC":
					$url = "http://ltc.block-explorer.com/tx/".$txid;
					break;
			}

		///////////////////// Change of code required when Virtual Currency added
?>
<p>Transaction Hash: <a href="<?=$url?>"><?=$txid?></a></p>

<p>Thanks,<br>
<?=NOREPLY?></p>

<p>P.S. Please do not reply to this email. </p>
<p>We do not spam. </p>
