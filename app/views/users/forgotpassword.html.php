<div class="row container-fluid">
	<div class="col-md-6" >
		<h4>Forgot password</h4>
		<?=$this->form->create("",array('url'=>'/users/forgotpassword','class'=>'form-group has-error')); ?>
		<?=$this->form->field('email', array('type' => 'text', 'label'=>'Your email','placeholder'=>'name@yourdomain.com','class'=>'form-control' )); ?>					<br>
		<?=$msg?><br>
		<?=$this->form->submit('Send password reset link' ,array('class'=>'btn btn-primary')); ?>					
		<?=$this->form->end(); ?>
	</div>
</div>