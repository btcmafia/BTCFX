<h2>Withdrawal Confirmation</h2>
<?php
/*
foreach($transaction as $foo => $bar) {
echo "<p>$foo: ";
print_r($bar);
echo '</p>';
}
*/

?>

<?php if(count($transaction)!=0){?>
<div class="alert alert-success">
<h4>Withdraw <b><?=abs($transaction['Amount'])?> <?=$currency?></b> to <b><?=$transaction['Address']?></b></h4>
</div>
<div class="row">
	<div class="col-md-6 well" >
	<form action="/in/paymentadmin/" method="post" id="PaymentForm" class="form-group">
	<input type="hidden" name="user_id" id="user_id" value="<?=$transaction['user_id']?>" class="form-control"/>
	<input type="hidden" name="username" id="username" value="<?=$username?>" class="form-control"/>
	<input type="hidden" name="currency" id="Currency" value="<?=$currency?>" class="form-control"/>	
	<input type="hidden" id="verify" value="<?=$transaction['verify.payment']?>" name="verify" class="form-control">
	<?=$this->form->field('password', array('type' => 'password', 'label'=>'Password', 'placeholder'=>'password','class'=>'form-control')); ?><br>

	<input type="submit" value="Confirm <?=$currency?> Withdrawal" class="btn btn-success" id="PaymentConfirm" onClick="document.getElementById('PaymentConfirm').disabled = true;$('#PaymentForm').submit();"> 
	</form>
	</div>
</div>
<?php } else { ?>
<div class="alert alert-dismissible alert-danger">
<p><strong>Invalid transaction!</strong> Has it been cancelled or processed already?</p>
</div>
<?php } ?>
