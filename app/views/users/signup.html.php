<?php
use app\models\Parameters;
$Comm = Parameters::find('first');
?>
<?php $this->form->config(array( 'templates' => array('error' => '<p class="alert alert-danger">{:content}</p>'))); 
?>
<div class="row container-fluid">
	<div class="col-md-6 well" >
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Sign In</h3>
			</div>
		</div>
		<?=$this->form->create($Users,array('class'=>'form-group has-error')); ?>
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="FirstNameIcon"></i>
					</span>
		<?=$this->form->field('firstname', array('label'=>'','placeholder'=>'First Name', 'class'=>'form-control','onkeyup'=>'CheckFirstName(this.value);' )); ?>
				</div>
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="LastNameIcon"></i>
					</span>
		<?=$this->form->field('lastname', array('label'=>'','placeholder'=>'Last Name', 'class'=>'form-control','onkeyup'=>'CheckLastName(this.value);' )); ?>
				</div>
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="UserNameIcon"></i>
					</span>
		<?=$this->form->field('username', array('label'=>'','placeholder'=>'username', 'class'=>'form-control','onkeyup'=>'CheckUserName(this.value);' )); ?>
				</div>
		<p class="label label-danger">Only characters and numbers, NO SPACES</p>				
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="EmailIcon"></i>
					</span>

		<?=$this->form->field('email', array('label'=>'','placeholder'=>'name@youremail.com', 'class'=>'form-control','onkeyup'=>'CheckEmail(this.value);'  )); ?>
				</div>
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="PasswordIcon"></i>
					</span>
		<?=$this->form->field('password', array('type' => 'password', 'label'=>'','placeholder'=>'Password', 'class'=>'form-control','onkeyup'=>'CheckPassword(this.value);' )); ?>
				</div>
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="Password2Icon"></i>
					</span>
		<?=$this->form->field('password2', array('type' => 'password', 'label'=>'','placeholder'=>'same as above', 'class'=>'form-control','onkeyup'=>'CheckPassword(this.value);' )); ?>
				</div>
			</div>				
		<?php // echo $this->recaptcha->challenge();?>
		<?=$this->form->submit('Sign up' ,array('class'=>'btn btn-primary btn-block')); ?>
		<?=$this->form->end(); ?>
	</div>
	<div class="col-md-6 well" >
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Advantages</h3>
			</div>
		</div>
		<h3>In Bitcoin We Trust: ibwt.co.uk</h3>
		<ul>
			<li>Fees are <strong><?=$Comm['value']?></strong>% per transaction.</li>
    	<li>Cold Storage, SSL and 256bit encryption.</li>
    <li>2FA login and coin withdrawal, with optional 3FA login.</li>
    <li>Deposits & Withdrawals immune to banking interference.</li>
    <li>Exchange available to all internationally and nationally.</li>
    <li>Fiat Deposits/Withdrawals currently only for UK residents.</li>
		</ul>

<p>To become an IBWT customer and use our platform and services, you only need the following;
<ul>
    <li>To trade BTC/LTC - registered email.</li>
    <li>To deposit fiat - registered email.</li>
    <li>To withdraw fiat - verified proof of address.</li>
    <li>To deposit/withdraw fiat over &pound;10,000 a day - valid government photo ID.</li>
</ul>
</p>
<p>Please make sure you check - <a href="/files/Withdrawal%20Verification.pdf" target="_blank">7 Easy Verifcation Steps</a> 
<p>For further details on verification, deposits and withdrawals, please check.
<ul>
    <li><a href="/company/verification">Verification</a></li>
    <li><a href="/company/funding">Funding</a></li>
</ul>		
</p>
Any issues please contact us at <a href="mailto:support@ibwt.co.uk">support@ibwt.co.uk</a>
</p>
		</div>
	</div>

