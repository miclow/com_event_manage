<?php 

jimport('joomla.application.module.helper');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
$module = JModuleHelper::getModule('mod_bt_login');

/*echo $this->state;*/
/*echo '<pre>'; print_r($this->stampduty); echo '</pre>';*/

$StartDate = $this->frontdata->data['page1']['start_date'];
$PublicLiabilityCover = $this->frontdata->data['page1']['liailitycover'];
$TotalPremium = $this->frontdata->data['page2']['totalpremium'];
$TotalExtras = '';
$Subtotal = $TotalPremium+$TotalExtras;
$CreditCardFee = $Subtotal*1.5/100;
$OrderTotal  = $Subtotal+$CreditCardFee;

$url = JUri::base().'administrator/components/com_event_manage/assets/css/progress-wizard.min';
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
<div class="page1 span12">
	<ul class="progress-indicator">
        	<?php 
        	foreach ($this->pageArr as $key => $value) {

        		 $page = 'page'.$value;
        		if(array_key_exists($page, $this->progress->data)){
					if($key != 'checkout'){
						$active = 'completed';		
					}
				}else{
						$active = '';		
				}
				if($key == 'checkout'){
						$active = 'active';		
				}
        		echo '<li class="'.$active.'">';
                echo '<span class="bubble"></span>';
                echo '<br><small>'.strtoupper($key).'</small>';
            	echo '</li>';
        	}
        	?>
        </ul>
	<div class="form-title"> <h1>Checkout Page 6</h1> </div>
	  <div class="form-body left span6">			
		<form action="" method="post" enctype="multipart/form-data" id="page2" class="form-validate">
		<div class="control-group">
			<div class="control-label span4">
				<label class="" for="event" id="event-lbl">Start Date</label>
			</div>
			<div class="controls span6">			
				<?php echo $this->frontdata->data['page1']['start_date']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label class="" for="event" id="event-lbl">Public Liability Cover</label>
			</div>
			<div class="controls span6">			
				<?php 
				$activityData = $this->frontdata->data['page1']['liailitycover']; 
				?>
			</div>
		</div>	
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Extra Charges
				</label>
			</div>
			<div class="controls span6">
				<?php echo $this->frontdata->data['page1']['perfomers']; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Order Total Preview
				</label>
			</div>
		</div>		
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Total Premium
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $TotalPremium; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Total Extras
				</label>
			</div>
			<div class="controls span6">
				<?php echo  'Sum Of Extras'; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Subtotal
				</label>
			</div>
			<div class="controls span6">
				<?php echo $Subtotal; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Credit Card Fee 
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $CreditCardFee; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Order Total  
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $OrderTotal; ?>			
			</div>
		</div>
		<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('creditcard'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('creditcard'); ?></div>
		</div>
		<div class="control-group">
				<div class="control-label">
					Your billing information must match the billing address for the credit card entered below or we will be unable to process your payment
				</div>
		</div>
		<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('cardnumber'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('cardnumber'); ?></div>
		</div>
		<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('expirationdate'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('expirationdate'); ?></div>
		</div>
		<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('CVV'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('CVV'); ?></div>
		</div>
		<div class="control-group">
				<div class="control-label"></div>
				<div class="controls">
				<input type="submit" class="btn btn-primary" name="submit" value="submit">
				</div>
	</div>	
	<input type="hidden" aria-required="true" value="<?php echo $StartDate; ?>" id="jform_premium" name="jform[startdate]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo $PublicLiabilityCover; ?>" id="jform_publicliailitycover" name="jform[publicliailitycover]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo $TotalPremium; ?>" id="jform_totalpremium" name="jform[totalpremium]" aria-invalid="true">
	<input type="hidden" name="page2" value="page2">
	</form>				
	</div>
	<div class="form-body right span5" >		
		<h3>Terms & Condition</h3>		
			
		<div class="control-group">			
			<div class="controls span6">
				<?php echo $term['description']; ?>			
			</div>
		</div>

	</div>

	<div class="control-group">
			<div class="control-label span4">				
			</div>
			<div class="controls span6">
				<a href="index.php?option=com_event_manage&view=declaration&step=5" value="Back" class="btn btn-primary" >BACK</a>
			</div>
	</div>
	<div>
		<?php //echo JModuleHelper::renderModule($module); ?>
	</div>

</div>