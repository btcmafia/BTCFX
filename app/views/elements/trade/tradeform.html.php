<?php
if('buy' == $type) {
	 $conn = 'with';
	 $best = 'Lowest Ask Price';
} else {
	 $conn = 'for';
	 $best = 'Highest Bid Price';
}
?>

     <div class="panel panel-info" >
                        <div class="panel-heading">
                               <h2 class="panel-title"  style="cursor:pointer;font-weight:bold" onclick="document.getElementById('Graph').style.display='block';"><?=ucfirst($type);?> <?=$first_curr?> <?=$conn?> <?=$second_curr?> <i class="glyphicon glyphicon-indent-left"></i></h2>
             </div>
<?=$this->form->create(null,array('id'=>'BuyForm')); ?>

<table class="table table-condensed " >
        <tr>
                <td width="50%">Your balance:
                <span id="BalanceSecond"> <?=$balance?></span> <?=$currency?>
                </td>
                <td><?=$best?>
                <span id="LowestAskPrice"> 0</span> <?=$second_curr?>
                </td>
        </tr>
</table>
<?=$this->form->field('Type', array('type' => 'hidden', 'value' => $type));?>     

<div class="form-group row">
      <label for="Amount" class="col-sm-2 col-sm-offset-1 control-label">Quantity</label>
      <div class="col-lg-6">
	<div class="input-group">
        <input class="form-control" id="Amount" name="Amount" placeholder="" type="text">
	<span class="input-group-addon"> <strong><?=$first_curr?></strong></span>     
	</div>
 </div>
</div>

<div class="form-group row">
      <label for="Price" class="col-sm-3 col-sm-offset-0 control-label"><span class="pull-right">Price<br /><small>(per <?=$first_curr?>)</small></span></label>
      <div class="col-sm-6">
	<div class="input-group">
        <input class="form-control" id="Price" name="Price" placeholder="" type="text">
	<span class="input-group-addon"> <strong><?=$second_curr?></strong></span>     
      </div>
	</div>
</div>

<!-- Advanced - Not yet implemented
<div class="form-group row">
	<label for="Dark" class="col-sm-3 control-label">Hide Order</label>
	<input class="checkbox" id="Dark" name="Dark" type="checkbox" checked="false" />	
</div>
-->

<div class="form-group row">
      <div class="col-sm-10 col-lg-offset-3">
        <button type="submit" id="submit-<?=$type?>" value="true" name="submit-<?=$type?>" class="btn btn-success">Place <?=ucfirst($type)?> Order</button>
      </div>
    </div>


<?=$this->form->end(); ?>

	</div>

