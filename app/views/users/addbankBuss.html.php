<br>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">Add / Edit Business Bank</h3>
	</div>
	<div class="panel-body">

<div class="row">
	<div class="col-md-6">
<p>This will un-set 'verified' status, you will have to verify the bank again.</p>
<?php
foreach($details as  $d){
?>
<?=$this->form->create('',array('url'=>'/users/addbankBussdetails')); ?>
<?=$this->form->field('accountname', array('label'=>'1. Account name','placeholder'=>'Account name','value'=>$d['bankBuss']['accountname'],'class'=>'form-control')); ?>
<?=$this->form->field('companyname', array('label'=>'1a. Company name','placeholder'=>'Company name','value'=>$d['bankBuss']['companyname'],'class'=>'form-control')); ?>
<?=$this->form->field('companynumber', array('label'=>'1b. Company number','placeholder'=>'Company number','value'=>$d['bankBuss']['companynumber'],'class'=>'form-control')); ?>
<?=$this->form->field('sortcode', array('label'=>'2. Sort code','placeholder'=>'Sort code','value'=>$d['bankBuss']['sortcode'],'class'=>'form-control' )); ?>
<?=$this->form->field('accountnumber', array('label'=>'3. Account number','placeholder'=>'Account number','value'=>$d['bankBuss']['accountnumber'],'class'=>'form-control')); ?>
<?=$this->form->field('bankname', array('label'=>'4. Bank name','placeholder'=>'Bank name','value'=>$d['bankBuss']['bankname'] ,'class'=>'form-control')); ?>
<?=$this->form->field('branchaddress', array('label'=>'5. Branch address','placeholder'=>'Branch address','value'=>$d['bankBuss']['branchaddress'],'class'=>'form-control')); ?>
<?=$this->form->submit('Save bank',array('class'=>'btn btn-primary btn-block')); ?>
<?=$this->form->end(); ?>
<?php }?>
	</div>
	<div class="col-md-6">
		<p>Sample bank cheque for adding bank details.</p>
		<img src="/img/Cheque.png" alt="sample bank cheque">	
		<p>At present we only support the following banks: 
<ul>
<li>Natwest</li>
<li>Lloyds</li>
<li>Barclays</li>
<li>TSB</li>
<li>HSBC</li>
<li>Royal Bank of Scotland</li>
<li>Co-Operative Bank</li>
<li>Sandander</li>
<li>Halifax</li>
<li>Handelsbanken</li>
</ul>
</p>
<p>If your bank is not listed, do not worry, please contact us via support@ibwt.co.uk and we will confirm whether or not your bank falls within our locality.</p>
	</div>
</div>
