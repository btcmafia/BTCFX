<h2>Update password</h2>

<?php
if(isset($error)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-danger col-sm-5">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>
</div>
<?php
 } if(isset($message)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-success col-sm-5">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success</strong> <?php echo $message; ?>
</div>
</div>
<?php } ?>


<?=$this->form->create("",array('url'=>"/in/changepassword/$key",'class'=>'col-md-5')); ?>
<?=$this->form->field('email', array('type' => 'text', 'label'=>'Your email address','placeholder'=>'email@domain.com','class'=>'form-control' )); ?>					
<?=$this->form->field('password', array('type' => 'password', 'label'=>'New Password','placeholder'=>'Password','class'=>'form-control' )); ?>
<?=$this->form->field('password2', array('type' => 'password', 'label'=>'Repeat new password','placeholder'=>'same as above','class'=>'form-control' )); ?>

<?php
if($TwoFactorEnabled) echo $this->form->field('2FA', array('type' => 'text', 'label' => 'Two Factor Code', 'placeholder' => 'enter code generated by your authenticator app', 'class' => 'form-control')); 
?>
<p>
<?=$this->form->submit('Update Password' ,array('class'=>'btn btn-primary')); ?>					
</p>
<?=$this->form->end(); ?>
