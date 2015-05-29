<?php
?>
<p>&nbsp;</p>
<div class="row container-fluid">
	<div class="col-md-6 well" >
		
		<div class="panel-body">
        <h3 style="margin-top:0; margin-bottom:15px;">Sign In</h3>
								<p>Please make sure you enter your <span style="color:red">username</span>, not your email. Your username & password are <span style="color:red">case sensitive</span>!</p>
			<?=$this->form->create(null,array('class'=>'form-group has-error')); ?>
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="UserNameIcon"></i>
					</span>
						<?=$this->form->field('username', array('label'=>'', 'onBlur'=>'SendPassword();', 'placeholder'=>'username', 'class'=>'form-control')); ?>
				</div>
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk"></i>
					</span>
			<?=$this->form->field('password', array('type' => 'password', 'label'=>'', 'placeholder'=>'password','class'=>'form-control')); ?>
				</div>
			</div>				

		
			<?=$this->form->submit('Login' ,array('class'=>'btn btn-primary btn-block','id'=>'LoginButton','disabled'=>'disabled')); ?>
			<?=$this->form->end(); ?>
			<a href="/users/forgotpassword">Forgot password?</a>
		</div>
	</div>

</div>
