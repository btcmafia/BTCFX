<?php

?>

<p class="alert alert-danger">
You can fund your <a href="/" >IBWT</a> account with <a href="/company/funding">Hard Currency</a> (UK only) or via bank wire transfers through <a href="/okpay">OKPAY</a> or <a href="/egopay">EGOPAY</a> </p>
<div class="row container-fluid">
	<div class="col-md-6 well" >
		
		<div class="panel-body">
        <h3 style="margin-top:0; margin-bottom:15px;">Login</h3>
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

			<div class="alert alert-danger"  id="LoginEmailPassword" style="display:none">
				<div class="form-group has-error">			
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-asterisk"></i>
						</span>
					<?=$this->form->field('loginpassword', array('type' => 'password', 'label'=>'','class'=>'span1','maxlength'=>'6', 'placeholder'=>'123456','class'=>'form-control')); ?>
					</div>		
				</div>		
				<small>Please check your registered email in 5 seconds. You will receive "<strong>Login Email Password</strong>" use it in the box below.</small>
			</div>		

			<div style="display:none" id="TOTPPassword" class="alert alert-danger">
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk"></i>
					</span>
			<?=$this->form->field('totp', array('type' => 'password', 'label'=>'','class'=>'span1','maxlength'=>'6', 'placeholder'=>'123456','class'=>'form-control')); ?>	
				</div>		
			</div>		
				<small><strong>Time based One Time Password (TOTP) from your smartphone</strong></small>	
			</div>
		
			<?=$this->form->submit('Login' ,array('class'=>'btn btn-primary btn-block','id'=>'LoginButton','disabled'=>'disabled')); ?>
			<?=$this->form->end(); ?>
			<a href="/users/forgotpassword">Forgot password?</a>
		</div>
	</div>
	<div class="col-md-6 well">
	
		<div style="padding-top:0;" class="panel-body">
		<h3 style="margin-top:0;">Sign up</h3>
		Don't have an account. <a href="/users/signup" >Signup</a><br>
		Please read the <a href="/company/termsofservice">terms of service</a> page before you sign up.<br>
		<h3>Security</h3>
		We use <strong>Two Factor Authentication</strong> for your account to login to <?=COMPANY_URL?>.<br>
		We use <strong>Time-based One-time Password Algorithm (TOTP)</strong> for login, withdrawal/deposits and settings.<br>
		Optional Google Authenticator can be activated via settings.<br>
Customers must password verify coin withdrawal.<br>
All coin withdrawals are admin approved for extra security.<br>
		<p><h3>TOTP Project and downloads</h3>
			<ul>
			<li><a href="http://code.google.com/p/google-authenticator/" target="_blank">Google Authenticator</a></li>
			<li><a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">TOTP Android App</a></li>
			<li><a href="http://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" target="_blank">TOTP iOS App</a></li>
			</ul>
		</p>
	</div>
</div>