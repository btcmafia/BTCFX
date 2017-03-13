<h2 class="title">New Customer</h2>

<?php if($success) { ?>
<p>Customer created.</p>
<p>
	The Customer ID is <?=$customer_id?><br />
	The Address ID is <?=$address_id?>
</p>
<?php } ?>

<?=$this->form->create($customer, array('class' => 'form-horizontal', 'id' => 'new_customer', 'name' => 'new_customer', 'url' => 'Office::newcustomer')); ?>

<?= $this->form->field('first_name', ['type' => 'text']) ?>
<?= $this->form->field('last_name', ['type' => 'text']) ?>
<?= $this->form->field('email', ['type' => 'text']) ?>

<?= $this->form->field('phone', ['type' => 'text']) ?>
<?= $this->form->field('address_1', ['type' => 'text']) ?>
<?= $this->form->field('address_2', ['type' => 'text', 'label' => '']) ?>
<?= $this->form->field('city', ['type' => 'text']) ?>
<?= $this->form->field('postcode', ['type' => 'text']) ?>

<?= $this->form->field('use_billing', ['type' => 'checkbox', 'label' => 'Use billing address for service address', 'checked' => 'true']) ?> 

<?= $this->html->link('Add different service address', '#', array('style' => 'font-weight:bold;', 'onclick' => 'showService()')) ?>
<div id="service_address_form" class="show">
<?= $this->form->field('service_contact_name', ['type' => 'text']) ?>
<?= $this->form->field('service_email', ['type' => 'text']) ?>
<?= $this->form->field('service_phone', ['type' => 'text']) ?>
<?= $this->form->field('service_address_1', ['type' => 'text']) ?>
<?= $this->form->field('service_address_2', ['type' => 'text', 'label' => '']) ?>
<?= $this->form->field('service_city', ['type' => 'text']) ?>
<?= $this->form->field('service_postcode', ['type' => 'text']) ?>
</div>


<?= $this->form->submit('Create Customer & New Job') ?>

<?=$this->form->end();?>

