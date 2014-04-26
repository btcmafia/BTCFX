<?php
foreach($pages as $page){
?>
<div class="row">
<h4 style="background-color:#000000;padding:5px "><?=$page['pagename']?></h4>
<?=$this->form->create($Pages,array('class'=>'form-group')); ?>
		<?=$this->form->field('title', array('label'=>'','placeholder'=>'Title', 'class'=>'form-control','value'=>$page['title'] )); ?>
		<?=$this->form->field('keywords', array('label'=>'','placeholder'=>'Keywords', 'class'=>'form-control','value'=>$page['keywords'] )); ?>		
		<?=$this->form->field('description', array('label'=>'','placeholder'=>'Description', 'class'=>'form-control','value'=>$page['description'] )); ?>		
		<?=$this->form->field('_id', array('type'=>'hidden','value'=>$page['_id'])); ?>				
		<?=$this->form->submit('Save' ,array('class'=>'btn btn-primary btn-block')); ?>
		<?=$this->form->end(); ?>
</form>
</div>	
<?php
}
?>
<h4 style="background-color:#000000;padding:5px ">Add a page</h4>
<?=$this->form->create($Pages,array('action'=>'pageadd','class'=>'form-group')); ?>
<?=$this->form->field('pagename', array('label'=>'','placeholder'=>'Page name', 'class'=>'form-control','value'=>'' )); ?>
<?=$this->form->submit('Add page' ,array('class'=>'btn btn-primary btn-block')); ?>
<?=$this->form->end(); ?>
