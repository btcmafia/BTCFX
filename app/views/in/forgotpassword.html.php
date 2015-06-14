<div class="row container-fluid">
	<div class="col-md-6" >
		<h4>Forgot password</h4>
		<?=$this->form->create("",array('url'=>'/in/forgotpassword','class'=>'form-group has-error')); ?>
		<?=$this->form->field('username', array('type' => 'text', 'label'=>'Username','placeholder'=>'Enter your username','class'=>'form-control' )); ?>					<br>
		<?=$msg?><br>
		<?=$this->form->submit('Send password reset link' ,array('class'=>'btn btn-primary')); ?>					
		<?=$this->form->end(); ?>
	</div>
</div>
