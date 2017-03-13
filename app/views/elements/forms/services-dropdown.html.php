<form id="select_service" name="select_service" method="post">
<input type="hidden" id="page" value="<?=$page?>"></span>
<select id='url' name='service_name'>
<?php

if(! $service_required) {
echo "<option id='' value='' selected>Select Service</option>";
}

foreach($services as $service => $foo) {

$service_id = $foo['service_id'];
$service_urlencoded = urlencode($service);

if('' == $service) continue;

echo "<option id='$service_id' value='$service_urlencoded'";
if($service_required == $service) echo ' selected';
echo ">$service</option>";
}
?>
</select>

  <script type="text/javascript">
  $('#url').change(function() {
//  var url =  window.location.href; // + $("#url").val();
  var url =  "http://fx.btc.la/office/schedule/" + $("#page").val() + '/' + $("#url").val();
  if(url !="") 
  { 
//   window.location.replace(url);
  $("#select_service").attr("action", url);
  $("#select_service").submit();
  }
  });
  </script>

</form>
