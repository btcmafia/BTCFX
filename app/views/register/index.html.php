<?php
use app\models\Parameters;
$Comm = Parameters::find('first');
?>
<p>&nbsp;</p>
<?php $this->form->config(array( 'templates' => array('error' => '<p class="alert alert-danger">{:content}</p>'))); 
?>
<div class="row container-fluid">
	<div class="col-md-6 well" >
	
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Free Registration</h3>
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
		<?=$this->form->submit('Register' ,array('class'=>'btn btn-primary btn-block')); ?>
		<?=$this->form->end(); ?>
	</div>
</div>
