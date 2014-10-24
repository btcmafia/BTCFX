<h4>Change password</h4>
<?=$this->form->create("",array('url'=>'/users/password/','class'=>'col-md-5')); ?>
<?=$this->form->field('email', array('type' => 'text', 'label'=>'Your email address','placeholder'=>'email@domain.com','class'=>'form-control' )); ?>					
<?=$this->form->field('password', array('type' => 'password', 'label'=>'New Password','placeholder'=>'Password','class'=>'form-control' )); ?>
<?=$this->form->field('password2', array('type' => 'password', 'label'=>'Repeat new password','placeholder'=>'same as above','class'=>'form-control' )); ?>
<?=$this->form->hidden('key', array('value'=>$key))?><br>
<?=$this->form->submit('Change' ,array('class'=>'btn btn-primary')); ?>					
<?=$this->form->end(); ?>
