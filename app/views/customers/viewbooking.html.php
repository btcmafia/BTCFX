<h1>Confirm Appointment</h1>

<?php
if(isset($error)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-danger col-sm-5">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>
</div>
<?php
 }
  else {


 if(isset($message)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-success col-sm-5">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success</strong> <?php echo $message; ?>
</div>
</div>
<?php
   }
?>
<p>Thank you</p>
<p>We will notify you as soon as the contractor has accepted the appointment.</p>
<?php
   } // no error 

?>


