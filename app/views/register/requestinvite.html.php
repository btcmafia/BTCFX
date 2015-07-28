<script>

 $(document).ready(function(){

    $("#toggle").click(function(){
        $("#already").toggle();
    });

    });

</script>

<h2>Request Invitation</h2>

<p>We are not quite ready for our beta launch, only selected people can currently gain access while we are gathering feedback and making sure things work.</p>

<p>If you want to be one of the first beta users, enter your details below and we'll notify you when the time is right.</p>

<div class="row col-md-8">


<?=$this->form->create($request_invite); ?>

<div class="form-group col-md-6">
<?=$this->form->field('email', array('label'=>'', 'placeholder'=>'Email Address', 'class'=>'form-control', 'id' => 'email')); ?>
</div>

<div class="form-group col-md-3">
<?=$this->form->submit('Submit' ,array('name' => 'request_submit', 'class'=>'btn btn-primary btn-block','id' => 'request_submit')); ?>
</div>

<?=$this->form->end(); ?>

</div>

<div class="clearfix"></div>

<p><a href="#" id="toggle">Already have an invitation?</a></p>

<div class="row col-md-8" id="already" style="display: none;">

<p>Enter your invitation code below.</p>


<?=$this->form->create($already_invite); ?>

<div class="form-group col-md-6">
<?=$this->form->field('email', array('label'=>'', 'placeholder'=>'Invitation Code', 'class'=>'form-control', 'id' => 'email')); ?>
</div>

<div class="form-group col-md-3">
<?=$this->form->submit('Start Registration' ,array('name' => 'already_submit', 'class'=>'btn btn-primary btn-block','id' => 'already_submit')); ?>
</div>

<?=$this->form->end(); ?>

</div>

