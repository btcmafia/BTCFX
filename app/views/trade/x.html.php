<h2>Buy & Sell</h2>

<?php
if(isset($error)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-danger col-sm-7 col-sm-offset-1">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>
</div>
<?php } if(isset($message)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-success col-sm-7">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong> <?=$message?>
</div>
</div>
<?php }
?>



<div class="row">

<div class="col-sm-5">
<?php
$type = 'buy';
$balance = $second_balance;
$currency = $second_curr;
echo $this->_render('element', 'trade/tradeform', compact('balance', 'currency', 'first_balance', 'second_balance', 'type', 'message', 'error'));?>
</div>

<div class="col-sm-5">
<?php
$type = 'sell';
$balance = $first_balance;
$currency = $first_curr;
echo $this->_render('element', 'trade/tradeform', compact('balance', 'currency', 'first_balance', 'second_balance', 'type', 'message', 'error'));?>
</div>

</div>

<div class="row col-sm-10">

<h2 style='text-align:center;'>Orderbook</h2>
<div class="col-sm-6">
<?php
$type = 'bids';
echo $this->_render('element', 'orderbook', compact('orders', 'type'));
?>
</div>

<div class="col-sm-6">
<?php
$type = 'asks';
echo $this->_render('element', 'orderbook', compact('orders', 'type'));
?>
</div>

</div>
