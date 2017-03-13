<h2>Search Customers</h2>

<?=$this->form->create('', array('class' => 'form-horizontal', 'id' => 'search_customers', 'name' => 'search_customers', 'url' => 'Office::searchcustomers')); ?>

<?= $this->form->field('query', ['type' => 'text']) ?>
<?= $this->form->select('field', array('first_name' => 'First Name', 'last_name' => 'Last Name', 'address_1' => 'Address', 'postcode' => 'Postcode', 'email' => 'Email', 'phone' => 'Phone'), array('id' => 'query', 'value' => $field)) ?>

<?= $this->form->submit('Search Customers') ?>
<?=$this->form->end();?>

<hr />

<?php


if( ($query != '') && ($field != '') ) { 
	
	echo "<h2>Search Results</h2>
	<div class='alert alert-info col-lg-4'>
	<p>Your search for customers with <b>$field</b> like <b>$query</b> returned <b>$count</b> results.</p>
	</div>";
 }

 if('' != count($results)) : 
foreach($results as $result) {
?>

<h3 style="clear:both"><a href="/office/viewcustomer/<?=$result->_id ?>"><?=$result->first_name?> <?=$result->last_name?></a></h3>

<div class="col-lg-3">

<p>
Tel: <?php if('' != $result->phone) { echo $result->phone; }?><br />
Email: <?php if('' != $result->email) { echo $result->email; }?><br />
</p>
<p>
<?=$result->address_1?><br />
<?php if('' != $result->address_2) { echo $result->address_2; }?><br />
<?php if('' != $result->city) { echo $result->city; }?><br />
<?php if('' != $result->postcode) { echo $result->postcode; }?><br />
</p>

</div>
<div class="col-lg-4">
<h4>Service Address</h4>
<?php

//print_r($result->service_address['address_1']);

if(count($result->service_addresses >  0)) {

echo $this->form->create('', array('class' => 'form-horizontal', 'id' => 'select_service_address', 'name' => 'select_service_address', 'url' => 'Office::selectserviceaddress')); 

/*
echo '<p>';
print_r($result . ": "); //print_r($value);
echo '</p>';
*/
$options = array();
$select = array();

foreach($result->service_addresses as $service_address_id => $value) {
	$select[$service_address_id] = "{$value['address_1']}, {$value['address_2']}, {$value['postcode']}";

	if($value['default']) $default = $service_address_id;
}
//print_r($select);

if(isset($default)) $options = array('value' => $default);

echo $this->form->select('service_address_id', $select, array('id' => 'service_address_id'), $options);
echo '&nbsp;' . $this->form->submit('Make Active'); 
echo $this->form->end();
}
?>
</div>


<hr />

<?php } endif; ?>

