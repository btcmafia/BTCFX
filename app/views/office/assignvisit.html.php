<h1 class="title">Assign Job</h1>

<?=$this->form->create($job, array('class' => 'form-horizontal', 'id' => 'assign_visit', 'name' => 'assign_visit', 'url' => 'Office::assignvisit'))?>

<?= $this->form->field('contractor_id', ['type' => 'hidden', 'value' => $contractor_id])?>
<?= $this->form->field('customer_id', ['type' => 'hidden', 'value' => $customer_id])?>
<?= $this->form->field('service_address_id', ['type' => 'hidden', 'value' => $service_address_id])?>
<?= $this->form->field('timeslot_id', ['type' => 'hidden', 'value' => $timeslot_id])?>
<?= $this->form->field('service_id', ['type' => 'hidden', 'value' => $service_id])?>
<?= $this->form->field('service_required', ['type' => 'hidden', 'value' => urlencode($service_required)])?>
<?= $this->form->field('nonce', ['type' => 'hidden', 'value' => $nonce])?>

<p>You are about to assign the following job to <b><?=$contractor_name?></b> at an hourly rate of <b>&pound;<?=$rate?></b>, with a minimum charge of <b><?=$min_charge?></b>.</p>

<div class="container">
<div class="row justify-content-start">
    <div class="col-4 label">Service Required:</div><div class="col-4"><?=$service_required?></div> 
    <div class="col-4 label">Date &amp; Time:</div><div class="col-4"><?=$date_nicename?></div> 
    <div class="col-4 label">Customer:</div><div class="col-4"><?=$customer_name?><br /><?=$customer_phone?><br /><?=$customer_email?></div> 
    <div class="col-4 label">Service Address:</div><div class="col-4"><?=$service_address?><p>Contact: <?=$service_address_contact_name?><br /><?=$service_address_phone?><br /><?=$service_address_email?></p></div> 
</div>

<div class="row">
<p>Enter a short description for the job below and click submit. The customer will receive an acknowledgement email asking them to click a link to confirm the request. They will then receive one more email when the contractor has accepted the appointment, usually within 30 minutes.</p>
<p><b>The appointment is not confirmed until both the customer and contractor accept the terms.</b></p>
</div>

<div class="row">
<?= $this->form->field('job_description', ['type' => 'text'])?>
<?= $this->form->field('customer_ref', ['type' => 'text'])?>
<?= $this->form->textarea('notes', ['label' => 'Notes'])?>
</div>

<div class="row">
<?=$this->form->submit('Submit')?>
</div>
<?=$this->form->end();?>
