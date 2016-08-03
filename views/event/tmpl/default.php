<?php 

jimport('joomla.application.module.helper');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
$module = JModuleHelper::getModule('mod_bt_login');


$event = $this->events;
$terms = $this->terms;
$activity = $this->activity;
$perfomers = $this->perfomers;
$annualincome = $this->annualincome;
$lcover = $this->lcover;
$currencysymbol = '$';
/*print_r($activity1['jform']['activity']);*/
/*print_r($this->front['activity']);*/
$activityArr = JRequest::get( 'post' );


$getactivity = $this->form->getValue('activity');
$getperfomers = $this->form->getValue('perfomers');
$getannualincome = $this->form->getValue('annualincome');
$getliailitycover = $this->form->getValue('liailitycover');
if(empty($this->renew)){
	$getdate = $this->form->getValue('start_date');
}

$url = JUri::base().'administrator/components/com_event_manage/assets/css/progress-wizard.min.css';
$document = JFactory::getDocument();
$document->addStyleSheet($url);
$document->addStyleSheet(JUri::base().'media/jui/css/jquery-ui.css');
$document->addScript(JUri::base().'/media/jui/js/jquery-1.10.2.js');
$document->addScript(JUri::base().'/media/jui/js/jquery-ui.js');
$this->progress->data = unserialize($this->progress->data);
/*print_r($this->progress->data);*/

?>

<?php
if(isset($this->errors) && $this->errors != ''){ ?>
<div class="error-class">
	<?php 
	JFactory::getApplication()->enqueueMessage($this->errors,'error');
	?>
</div>
<?php } ?> 
<style type="text/css">
	label{
		display: inline !important;
	}
	
</style>

<script type="text/javascript">

	js = jQuery.noConflict();

	js(document).ready(function () {
	
		js('#jform_start_date').datepicker({ dateFormat: 'dd-mm-yy', minDate: 0, maxDate: "+30D" });

	});
</script>
<div class="page1">
	<ul class="progress-indicator">
		<?php
		
		if(!empty($this->sid)){
				$sid = '&sid='.$this->sid;
		} 
		foreach ($this->pageArr as $key => $value) {
			
			$page = 'page'.$value;
			if(array_key_exists($page, $this->progress->data)){
				if($key != 'event'){
					$active = 'completed';		
					$color = '#65d074';
				}
			}else{
				$active = '';		
			}
			if($key == 'event'){
				$active = 'active';
				$color = '#337AB7';		
			}
			if(empty($active)){
				$color = '#bbbbbb';	
			}
			if(strtoupper($key) == 'EVENT'){
				$value = 'back';
			}
			$url = JRoute::_('index.php?option=com_event_manage&view='.$key.'&step='.$value.$sid);
			echo '<li class="'.$active.'">';
			echo '<span class="bubble"></span>';
			echo '<br><small>';
			if(strtoupper($key) == 'EVENT') { 
				echo	'<a href="'.$url.'"><h4>'.strtoupper('Requirements').'</h4></a>' ;
			}else{ 
				echo	'<a href="'.$url.'" style="color:'.$color.'"><h4>'.strtoupper($key).'</h4></a>' ;
			}
			echo '</small>';
			echo '</li>';
		}
		?>
	</ul>
	<div class="form-title">
		<h3>Page 1: Requirements</h3> 
	</div>
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
				<div class="control-label"><?php echo $this->form->getLabel('insurencename'); ?></div>
				<div class="controls">
					<!-- <input type="text" aria-required="true" required="required" class="required" value="<?php echo $this->frontdata['insurencename']; ?>" id="jform_insurencename" name="jform[insurencename]" aria-invalid="true"> -->
					<?php echo $this->form->getInput('insurencename'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('activity'); ?></div>
				<div class="row-fluid top15">
					<?php //echo $this->form->getInput('activity'); 
					$a=1;
					foreach ($activity as $value) {
							# code...
						$chek = '';
						if(!empty($getactivity)){
							if(in_array($value->price, $getactivity)){
								$chek = 'checked="checked"';
							}else{
								$chek = '';
							}
						}

						if($a%3 == 1){
							echo '<div class="span12" style="margin-left:0";>';	
						}
						echo '<div class="span4">';
						echo '<label for="jform_activity'.$a.'">'.'<input type="checkbox" '.$chek.' value="'.$value->price.'"name="jform[activity][]" id="jform_activity'.$a.'"> '.$value->price.'</label>';
						echo '</div>';
						
						if($a%3 == 0){
							echo '</div>';
						}

						$a++;
					}
					?>
				</div>
			</div>
			<div>&nbsp;</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0";>
					<div class="control-group span4">
						<div class="control-label"><?php echo $this->form->getLabel('perfomers'); ?></div>
						<div class="controls">
							<select aria-required="true" required="required" class="required" name="jform[perfomers]" id="jform_perfomers" aria-invalid="false">
								<option value="">Please Select</option>
								<?php 
								/*echo $this->form->getInput('perfomers'); */
								foreach ($perfomers as $pvalue) {
									$pchek = '';
									if(!empty($getperfomers)){
										if($pvalue->price == $getperfomers){
											$pchek = 'selected="selected"';
										}else{
											$pchek = '';
										}
									}

									echo '<option value="'.$pvalue->price.'" '.$pchek.'>'.$pvalue->name.'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="control-group span4">
						<div class="control-label"><?php echo $this->form->getLabel('annualincome'); ?></div>
						<div class="controls">
							<select aria-required="true" required="required" class="required" name="jform[annualincome]" id="jform_annualincome">
								<option value="">Please Select</option>
								<?php 
								/*echo $this->form->getInput('annualincome'); */
								foreach ($annualincome as $incomevalue) {
									$aichek = '';
									if(!empty($getannualincome)){
										if($incomevalue->price == $getannualincome){
											$aichek = 'selected="selected"';
										}else{
											$aichek = '';
										}
									}

									echo '<option value="'.$incomevalue->price.'" '.$aichek.'>'.$incomevalue->name.'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="control-group span4">
						<div class="control-label"><?php echo $this->form->getLabel('postcode'); ?></div>
						<div class="controls">
							<!-- <input type="text" aria-required="true" required="required" class="required" value="" id="jform_postcode" name="jform[postcode]" aria-invalid="true"> -->
							<?php echo $this->form->getInput('postcode'); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0";>
					<div class="control-group span4">
						<div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('email'); ?></div>
					</div>
					<div class="control-group span4">
						<div class="control-label"><?php echo $this->form->getLabel('confirmemail'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('confirmemail'); ?></div>
					</div>
					<div class="control-group span4">
						<div class="control-label"><?php echo $this->form->getLabel('start_date'); ?></div>
						<div class="controls">
								<input type="text" title="" name="jform[start_date]" readonly="true" id="jform_start_date" value="<?php echo $getdate; ?>" maxlength="45" class="required hasTooltip" required="required" aria-required="true">
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0";>
					<div class="control-group span4">
						<div class="control-label">
							<?php echo $this->form->getLabel('liailitycover').' :&nbsp;'; ?>
							<label> <?php echo $currencysymbol.number_format($lcover[0]->name); ?></label>
						</div>
						<div class="controls">
							<input type="hidden" id="jform_liailitycover" name="jform[liailitycover]" value="<?php echo $lcover[0]->name; ?>" >
						</div>
					</div>	
				</div>
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