<h2>Forgot password</h2>

<?php if(isset($msg)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-success col-sm-6">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <?=$msg?>
</div>
</div>
<?php } ?>

<div class="row container-fluid">
	<div class="col-md-6" >
		<?=$this->form->create("",array('url'=>'/in/forgotpassword','class'=>'form-group has-error')); ?>
		<?=$this->form->field('username', array('type' => 'text', 'label'=>'Username','placeholder'=>'Enter your username','class'=>'form-control' )); ?>					<br>
		<?=$this->form->submit('Send password reset link' ,array('class'=>'btn btn-primary')); ?>					
		<?=$this->form->end(); ?>
	</div>
</div>
