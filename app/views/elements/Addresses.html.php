<?php
if(count($addresses) >= 1) {
?>
<table class="table table-striped table-hover ">
  <thead>
    <tr>
      <th>Alternative Addresses (click to set as default and show QR code above)</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
<?php
	foreach($addresses as $address) {
?>
   <tr>
      <td><a href="/in/makedefault/<?=$address['btc_address']?>/"><?php echo $address[$type]; ?></a></td>
      <td>&nbsp;</td>
   </tr>
<?php
	}
?>
  </tbody>
</table>
<?php } ?>
