<div id="customer-new-service-address">

<h4>New Service Address <small>(<a href="#" id="close-new-address">close</a>)</small></h4>

<?=$this->form->create($address, array('class' => 'form-horizontal', 'id' => 'new_address', 'name' => 'new_address', 'url' => 'Office::newserviceaddress')); ?>

<?= $this->form->field('customer_id', ['type' => 'hidden', 'value' => $customer->_id]) ?>

<?= $this->form->field('address_1', ['type' => 'text']) ?>
<?= $this->form->field('address_2', ['type' => 'text', 'label' => '']) ?>
<?= $this->form->field('city', ['type' => 'text']) ?>
<?= $this->form->field('postcode', ['type' => 'text']) ?>

<?= $this->form->field('contact_name', ['type' => 'text']) ?>
<?= $this->form->field('phone', ['type' => 'text']) ?>
<?= $this->form->field('email', ['type' => 'text']) ?>

<?= $this->form->submit('Save Address') ?>

<?=$this->form->end();?>

</div>
