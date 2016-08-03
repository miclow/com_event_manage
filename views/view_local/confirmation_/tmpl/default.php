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
				if($key != 'confirmation'){
					$active = 'completed';
					$color = '#65d074';			
				}
			}else{
				$active = '';		
			}
			if($key == 'confirmation'){
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
	<div class="form-title"> <h1>Confirmation Page 7</h1> </div>
	<div class="form-body left span9">			
		<form action="" method="post" enctype="multipart/form-data" id="page7" class="form-validate">
			<div class="control-group">
				<div class="control-label">
					Thank you for purchasing your insurance with Action Entertainment 
					Your documents are being emailed to you now.
				</div>
			</div><br>
			You will receive:
			<div class="control-group">
				<div class="control-label span3">
					A Tax Invoice & Policy Schedule
				</div>
			</div><br>
			<div class="control-group">
				<div class="control-label span3">
					A certificate of currency
				</div>
			</div><br>
			<div class="control-group">
				<div class="control-label span3">
					SPolicy wording document
				</div>
			</div><br>
			<div class="control-group">
				<div class="control-label span3">
					Confirmation of your answers
				</div>
			</div><br>
			Important Information
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Order Type: 
					</label>
				</div>
				<div class="controls">
					New		
				</div>
			</div>	
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Your Policy Number:
					</label>
				</div>
				<div class="controls">
					<?php echo $this->frontdata->policy_number; ?>
				</div>
			</div>	
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Your Order Number is:
					</label>
				</div>
				<div class="controls">
					<?php echo $this->frontdata->id; ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Your documentation has been sent to:
					</label>
				</div>
				<div class="controls">
					<?php echo $this->frontdata->data['page1']['email']; ?>		
				</div>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Insurer:
					</label>
				</div>
				<div class="controls">
					<?php 
					echo $this->insurername.'<br/>';
					echo $this->insureraddress.'<br/>'; 
					?>
				</div>
			</div>					
			<div class="control-group">
				<div class="control-label span3">
					<label class="" for="event" id="event-lbl">Policy Inception:</label>
				</div>
				<div class="controls">			
					<?php echo $this->frontdata->data['page1']['start_date']; ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label class="" for="event" id="event-lbl">Policy Expiry:</label>
				</div>
				<div class="controls">			
					<?php 
						if($this->frontdata->data['page1']['start_date'] != ""){
							$expirationdate = date("d-m-Y",strtotime('+1 year',strtotime($this->frontdata->data['page1']['start_date'])));
							echo $expirationdate;
						}
					?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Total Premium:
					</label>
				</div>
				<div class="controls">
					<?php echo  $TotalPremium; ?>			
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					Should you have any questions please contact us at: 
					Email: entertainment@actioninsurance.com.au
					Telephone: 1300 655 424
				</div>
			</div>
			<div class="control-group pull-left MyMargin">
				<div class="controls">
					<a value="Back" id="print" class="btn btn-primary" >Print</a>
				</div>
			</div>
			<div class="control-group pull-left MyMargin">
				<div class="controls">
					<a href="<?php echo JRoute::_('index.php?option=com_event_manage&view=dashboard'); ?>" value="Back" class="btn btn-primary" >My Account</a>
				</div>
			</div>
			<div class="control-group pull-left MyMargin">
				<div class="controls">
					<a href="index.php?option=com_users&task=user.logout&<?php echo $userToken; ?>=1" value="Logout" class="btn btn-primary" >Logout</a>
				</div>
			</div>
	
			<input type="hidden" name="page7" value="page7">
		</form>				
	</div>
	</div>