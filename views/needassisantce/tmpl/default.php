<?php 

jimport('joomla.application.module.helper');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
$module = JModuleHelper::getModule('mod_bt_login');
$user = JFactory::getUser();
$userToken = JSession::getFormToken();
$returnurl = JRoute::_('index.php?option=com_event_manage&view=event');
/*echo $this->state;*/
/*echo '<pre>'; print_r($this->stampduty); echo '</pre>';*/
$Extrachreges = $this->Extrachreges;
$charges = 0;
foreach ($Extrachreges as $evalue) {
	$charges += $evalue->price;
}


$StartDate = $this->frontdata->data['page1']['start_date'];
$PublicLiabilityCover = $this->frontdata->data['page1']['liailitycover'];
$TotalPremium = $this->frontdata->data['page2']['totalpremium'];


$url = JUri::base().'administrator/components/com_event_manage/assets/css/progress-wizard.min.css';
$document = JFactory::getDocument();
$document->addStyleSheet($url);

$this->progress->data = unserialize($this->progress->data);

?>

<?php
if(isset($this->errors) && $this->errors != ''){ ?>
<div class="error-class">
	<?php echo $this->errors; ?>
</div>

<?php } ?> 
<script type="text/javascript">
	js = jQuery.noConflict();

	js(document).ready(function () {
		js("#print").click(function(){
			
			var w = window.open('', '', 'width=800,height=600,resizeable,scrollbars');
			w.document.write(js("#page7").html());
			 w.document.close(); // needed for chrome and safari
			 javascript:w.print();
			 w.close();
			 return false;
			});
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
				if($key != 'needassisantce'){
					$active = 'completed';
					$color = '#65d074';			
				}
			}else{
				$active = '';		
			}
			if($key == 'needassisantce'){
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
				echo	'<a href="'.$url.'" style="color:'.$color.'"><h4>'.strtoupper('Requirements').'</h4></a>' ;
			}else{ 
				echo	'<a href="'.$url.'" style="color:'.$color.'"><h4>'.strtoupper($key).'</h4></a>' ;
			}
			echo '</small>';
			echo '</li>';
		}
		?>
	</ul>
	<div class="form-title"> <h3>Assistance Page</h3> </div>
		<div class="form-body left span9">			
				<div class="control-group">
					<div class="control-label">
						<strong>One of our staff members will contact you shortly. Thanks</strong><br>
					</div>
				</div>
				<input type="hidden" name="page8" value="page8">
		</div>
	</div>