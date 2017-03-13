<h2 class="title"><?=$title?></h2>

<?php if(isset($error)) { ?>
<div class="row">
<div class="alert alert-danger col-md-2 col-md-offset-1">
<?=$error?>
</div>
</div>
<?php } ?>

<?=$this->_render('element', 'bookings/results', compact('bookings', $context))?>
