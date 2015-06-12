<h2>API Settings</h2>
<?php if($APIEnabled) { 
$enable_disable = 'Disable';
?>
<p>Your API is currently enabled.</p>
<p>Update your API settings below.</p>
<?php } else { 
$enable_disable = 'Enable';
?>
<p>Your API is currently disabled.</p>
<p>You can enable it below. You can also alter its settings prior to enabling.</p> 
<?php }
if(isset($api_key)) { ?>
<div class="col-sm-6">
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">API Key &amp; Secret</h3>
  </div>
  <div class="panel-body">
<p>API KEY: <?=$api_key?></p>
<p>SECRET:  <?=$api_secret?></p>
  </div>
</div>
</div>
<?php } ?>

<?=$this->form->create($saved, array('name' => 'api', 'class' => 'form-horizontal col-lg-12')); ?>

<fieldset>
    <legend><h2>View / Update Settings</h2></legend>


<?php if(isset($error)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-danger col-sm-4">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong> <?=$error?>
</div>
</div>

<?php } elseif(isset($message)) { ?>
<div class="row">
<div class="alert alert-dismissible alert-success col-sm-4">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong> <?=$message?>
</div>
</div>
<?php } ?>


<div class="form-group">
        <input class="" type="radio" name="api_action" id="view_credentials" value="view_credentials" />
        <label for="view_credentials" class="control-label">View API Key &amp; Secret</label>
</div>

<div class="form-group">
        <input class="col-sm-offset-2" type="radio" name="api_action" id="enable_disable" value="enable_disable" />
        <label for="enable_disable" class="control-label"><?=$enable_disable?> API</label>
</div>

<div class="form-group">
        <input class="col-sm-offset-2" type="radio" name="api_action" id="new_credentials" value="new_credentials" />
        <label for="new_credentials" class="control-label">Generate New API Key &amp; Secret</label>
</div>

<div class="form-group">
        <input class="col-sm-offset-2" type="radio" name="api_action" id="update_withdrawals" value="update_withdrawals" />
        <label for="update_withdrawals" class="control-label">Update Addresses Your API can Withdraw To</label>
</div>

<div class="row">
<div class="form-group col-sm-4">
      <label for="AddressesBTC" class="control-label">Allowed Withdrawal BTC Addresses</label>
        <textarea class="form-control" id="AddressesBTC" name="AddressesBTC" placeholder="One address per line"></textarea>
      </div>
</div>

<div class="row">
<div class="form-group col-sm-4">
      <label for="AddressesCC" class="control-label">Allowed Withdrawal Asset Addresses</label>
        <textarea class="form-control" id="AddressesCC" name="AddressesCC" placeholder="One address per line"></textarea>
</div>
</div>

<div class="row">
<div class="form-group col-sm-4">
      <label for="Password" class="control-label">Password</label>
        <input class="form-control" id="Password" name="Password" placeholder="Password" type="password">
      </div>
</div>

<?php if($TwoFactorEnabled) { ?>
<div class="row">
<div class="form-group col-sm-4">
      <label for="2FA" class="control-label">Two Factor Code</label>
        <input class="form-control" id="2FA" name="2FA" placeholder="One Time Password" type="password">
      </div>
</div>
<?php } ?>

<div class="row">
<div class="form-group col-sm-4">
        <button type="submit" id="submit-api" value="true" name="submit-api" class="btn btn-primary">Update API Settings</button>
      </div>
    </div>
</fieldset>
<?=$this->form->end();?>

