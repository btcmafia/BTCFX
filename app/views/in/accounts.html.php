<h2>Summary of Accounts</h2>

<div class="col-md-7">


<table class="table table-striped table-hover ">
  <thead>
    <tr>
      <th>Currency</th>
      <th>Bitcoin</th>
      <th>The Coloured Pound</th>
      <th>Ducat</th>
    </tr>
  </thead>
  <tbody>
<?php
//var_dump($data);
	foreach($data as $key => $value) {
?>
<!--<tr><td colspan=4><?php// print_r($value); ?></td></tr>-->
    <tr>
      <td><?=$key?></td>
      <td><?=$value['btc']?></td>
      <td><?=$value['tcp']?></td>
      <td><?=$value['dct']?></td>
    </tr>
<?php
	}
?>
  </tbody>
  </table>


</div>
