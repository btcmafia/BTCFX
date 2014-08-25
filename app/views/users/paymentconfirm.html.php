<?php if(count($transaction)!=0){?>
<h4>Withdraw <?=abs($transaction['Amount'])?> <?=$currency?> to <?=$transaction['address']?></h4>
<div class="row">
	<div class="col-md-6 well" >
	<form action="/users/payment/" method="post" id="PaymentForm" class="form-group">
	<input type="hidden" name="username" id="Username" value="<?=$transaction['username']?>" class="form-control"/>
	<input type="hidden" name="currency" id="Currency" value="<?=$currency?>" class="form-control"/>	
	<input type="hidden" id="verify" value="<?=$transaction['verify.payment']?>" name="verify" class="form-control">
	<?=$this->form->field('password', array('type' => 'password', 'label'=>'Password', 'placeholder'=>'password','class'=>'form-control')); ?><br>
	<input type="submit" value="Confirm <?=$currency?> Withdrawal" class="btn btn-success" id="PaymentConfirm" onClick="document.getElementById('PaymentConfirm').disabled = true;$('#PaymentForm').submit();"> 
	</form>
	</div>
</div>
<?php }?>
<h4>You have withdrawn the <?=$currency?> or no transaction is authorised!</h4>