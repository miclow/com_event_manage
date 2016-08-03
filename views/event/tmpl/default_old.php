<?php 

jimport('joomla.application.module.helper');
JHTML::_('behavior.formvalidation');
$module = JModuleHelper::getModule('mod_bt_login');


$event = $this->events;
$terms = $this->terms;
?>

<?php
if(isset($this->errors) && $this->errors != ''){ ?>
	<div class="error-class">
		<?php echo $this->errors; ?>
	</div>

<?php } ?> 
<div></di>
<div class="page1 span12">

	<div class="form-title"> <h1>Page 1</h1> </div>

	<div class="form-body">		
		<form action="" method="post" enctype="multipart/form-data" id="page1" class="form-validate">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php //echo $this->form->getLabel('created_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('event'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('event'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('term'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('term'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('start_date'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('start_date'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('end_date'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('end_date'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"></div>
				<div class="controls"><?php echo $this->form->getInput('next'); ?></div>
			</div>		
			<div>
				<?php //echo JModuleHelper::renderModule($module); ?>
			</div>
		</form>		
	</div>

</div>