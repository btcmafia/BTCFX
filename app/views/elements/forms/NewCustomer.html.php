<div id="myNav" class="overlay">

  <!-- Button to close the overlay navigation -->
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

<div class="overlay-content">


<?=$this->form->create('', array('class' => 'form-horizontal', 'id' => 'new_customer', 'name' => 'new_customer', 'url' => 'Office::newcustomerandjob')); ?>

<?= $this->form->field('first_name', ['type' => 'text']) ?>
<?= $this->form->field('last_name', ['type' => 'text']) ?>
<?= $this->form->field('email', ['type' => 'text']) ?>
<?= $this->form->field('phone', ['type' => 'text']) ?>
<?= $this->form->field('address_1', ['type' => 'text']) ?>
<?= $this->form->field('address_2', ['type' => 'text', 'label' => '']) ?>
<?= $this->form->field('city', ['type' => 'text']) ?>
<?= $this->form->field('postcode', ['type' => 'text']) ?>
<?= $this->form->submit('Create Customer & New Job') ?>

<?=$this->form->end();?>

</div>
</div>
</div>

