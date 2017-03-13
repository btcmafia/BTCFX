<script>
 $(document).ready(function(){

        $("#new-address").click(function(){
	$("#customer-new-service-address").show("slow");
	});

        $("#close-new-address").click(function(){
	$("#customer-new-service-address").hide("slow");
	});

	$("#customer-new-service-address").hide();
	});

</script>

<div class="row">
<h1 class="col-lg-offset-4"><?=$customer->first_name ?> <?=$customer->last_name ?></h1>
</div>

<div class="row">

<div class="col-lg-offset-1 col-lg-4">

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Billing Address</h3>
  </div>

<div class="panel-body">
<p>
<?=$customer->address_1?><br />
<?php if('' != $customer->address_2) { echo $customer->address_2; }?><br />
<?php if('' != $customer->city) { echo $customer->city; }?><br />
<?php if('' != $customer->postcode) { echo $customer->postcode; }?><br />
</p>
<p>
Tel: <?php if('' != $customer->phone) { echo $customer->phone; }?><br />
Email: <?php if('' != $customer->email) { echo $customer->email; }?><br />
</p>

</div>
</div>

</div><!-- billing row -->


<div class="col-lg-offset-0 col-lg-6">


<div class="panel panel-default">
  <div class="panel-heading">
<h3 class="panel-title">Service Addresses <small>(<a href="#" id="new-address">Create New</a>)</small></h3>

<?=$this->_render('element', 'forms/new-service-address', compact('customer')) ?>
  </div>

<div class="panel-body">
<?php
echo $this->form->create('', array('class' => '', 'id' => "set_service_address", 'name' => "set_service_address", 'url' => 'Office::selectserviceaddress'));

foreach($customer->service_addresses as $address_id => $address) {

	echo '<div class="row"><div class="col-lg-4">';	
	if('' != $address->address_1) { echo $address->address_1 . '<br />'; }
	if('' != $address->address_2) { echo $address->address_2 . '<br />'; }
	if('' != $address->city) { echo $address->city . '<br />'; }
	if('' != $address->postcode) { echo $address->postcode . '<br />'; }
	echo '</div>';

	echo '<div class="col-lg-5">';
	if('' != $address->contact_name) { echo 'Contact Name: ' . $address->contact_name . '<br />'; }
	if('' != $address->phone) { echo 'Contact Phone: ' . $address->phone . '<br />'; }
	if('' != $address->email) { echo 'Contact Email: ' . $address->email . '<br />'; }
	echo '</div></div>';	

if($address_id == $active_service_address_id) $checked = true;
else $checked = false;
echo $this->form->field('service_address_id', ['value' => $address_id, 'id' => "select_address_$address_id", 'type' => 'radio', 'label' => 'Make Active', 'checked' => $checked]);
echo $this->form->submit('Update');

	echo '<hr />';
}
echo $this->form->end();
?>
</div>
</div>

</div><!-- service row -->

</div><!-- row -->

