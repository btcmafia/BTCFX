<h2>Trade Page</h2>

<?php
if(isset($error)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-danger col-sm-4">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>
</div>
<?php } if(isset($message)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-success col-sm-4">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong> <?=$message?>
</div>
</div>
<?php }
?>

<div class="row">

<div class="col-sm-4">
<?php
$type = 'buy';
$balance = $second_balance;
$currency = $second_curr;
echo $this->_render('element', 'trade/tradeform', compact('balance', 'currency', 'first_balance', 'second_balance', 'type', 'message', 'error'));?>
</div>

<div class="col-sm-4 ">
<?php
$type = 'sell';
$balance = $first_balance;
$currency = $first_curr;
echo $this->_render('element', 'trade/tradeform', compact('balance', 'currency', 'first_balance', 'second_balance', 'type', 'message', 'error'));?>
</div>

</div>
