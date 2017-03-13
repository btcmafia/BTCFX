<script>
 $(document).ready(function(){
        $("#use_timesheet").change(function(){

          if(this.checked) {
    $("#m").hide("slow");
    $("#t").show("slow");
    $("#below").show();
  }
  else {
    $("#m").show("slow");
    $("#t").hide("slow");
    $("#below").hide();
   }
    });
    $("#m").hide("slow");
    $("#t").show("slow");
    $("#below").show();

    $("#CompletedStatus").change(function() {

	var cs = $("#CompletedStatus").val();

	if(( cs == '' ) || (cs == 2)) {
	$("#cs").hide("slow");
	}
	else if((cs==0) || (cs==1)) {
	$("#cs").show("slow");
	}
     });
	$("#cs").hide("slow");
    });

</script>


<h2 class="title"><?=$title?></h2>

<?=$this->form->create($jobreport, array('class' => 'form-horizontal', 'id' => 'jobreport', 'name' => 'jobreport', 'url' => 'Contractors::jobreport')); ?>

<?= $this->form->field('short_description', ['type' => 'text', 'label' => 'Enter a short description of work carried out']) ?>

<div class="row">
      <label for="notes" class="control-label">Final comments / notes</label>
</div>
<div class="row">
<?= $this->form->textarea('notes', ['label' => 'Comments / notes'])?>
</div>

<div class="row">
<label for="use_timesheet">Use the timesheet details <span id="below">below</span></label>
<input class="col-sm-offset-2" type="checkbox" name="use_timesheet" id="use_timesheet" value="1" checked="true" />
</div>


<div class="row" id="t">
<?= $this->_render('element', 'timesheet', compact($timesheet)) ?>
</div>

<div class="row" id="m">
<?= $this->form->field('manual_minutes', ['type' => 'text', 'label' => 'Chargable Time (min)', 'size' => '5']) ?>
</div>

<?= $this->form->field('materials_cost', ['type' => 'text', 'size' => '4'])?>
<?= $this->form->field('materials_description', ['type' => 'text'])?>

<div class="row">
<?php
$completed_options = array('Another appointment by me is required',
			   'An appointment by another engineer is required',
			   'No further appointments are required');
?>
<select name="completed_status" id="CompletedStatus">
<option value='' selected>Select a completed status</option>
<?php foreach($completed_options as $key => $option) { ?>
<option value="<?=$key?>"><?=$option?></option>
<?php } ?>
</select>
</div>

<div id="cs">
<div class="row">
<label for="">Description of work still required</label>
</div>
<div class="row">
<?=$this->form->textarea('new_service_required') ?>
</div>
</div>

<div class="row" id="submit">
<?=$this->form->submit('Submit Job Report') ?>
</div>

<?=$this->form->end();?>



