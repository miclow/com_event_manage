<?php 

jimport('joomla.application.module.helper');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
$module = JModuleHelper::getModule('mod_bt_login');

$event = $this->event;
$term = $this->term;
$premium = $this->premium;
//echo '<pre>'; print_r($event); print_r($term); echo '</pre>';
?>

<?php
if(isset($this->errors) && $this->errors != ''){ ?>
	<div class="error-class">
		<?php echo $this->errors; ?>
	</div>

<?php } ?> 
<div class="page1 span12 actionform">

	<div class="form-title">
<nav>
	<ol class="cd-breadcrumb triangle">
	<li><em>Requirements</em></li>
	<li class="current"><em>Quote</em></li>
	<li><em>Proposal</em></li>
	<li><em>Name&Address</em></li>
	<li><em>Declaration</em></li>
        <li><em>Checkout</em></li>
        <li><em>Confirmation</em></li>
</ol></nav> </div>
	  <div class="form-body left span4">			
		<form action="" method="post" enctype="multipart/form-data" id="page2" class="form-validate">
		<div class="control-group">
			<div class="control-label span4">
				<label class="" for="event" id="event-lbl">Name of Insured</label>
			</div>
			<div class="controls span6">			
				<?php echo $this->frontdata->data['page1']['insurencename']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label class="" for="event" id="event-lbl">Occupation</label>
			</div>
			<div class="controls span6">			
				<?php 
				$activityData = $this->frontdata->data['page1']['activity']; 
				$acdata = '';
				foreach($activityData as $activdata){
					$acdata .= $activdata.',';

				}
				echo substr($acdata,0,-1);
				?>
			</div>
		</div>	
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Number of Performers
				</label>
			</div>
			<div class="controls span6">
				<?php echo $this->frontdata->data['page1']['perfomers']; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Estimated Annual Income
				</label>
			</div>
			<div class="controls span6">
				<?php echo $this->frontdata->data['page1']['annualincome']; ?>
			</div>
		</div>		
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Postcode
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $this->frontdata->data['page1']['postcode']; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Email Address 
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $this->frontdata->data['page1']['email']; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Start Date 
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $this->frontdata->data['page1']['start_date']; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Public Liability Cover  
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $this->frontdata->data['page1']['liailitycover']; ?>			
			</div>
		</div>
		<?php 
		$premiumvalue = round($premium[0]->premium,2);
		$brokerfee = round($premium[0]->brokerfee,2);
		$SubTotal = round($premiumvalue+$brokerfee,2);
		$GSTPremium = round($premiumvalue*0.1,2);
		$GSTfee = round($brokerfee*0.1,2);
		$StampDuty = round(9 * ($premiumvalue+$GSTPremium)/100,2);
		$TotalPremium = round($SubTotal+$StampDuty+$GSTPremium+$GSTfee,2);
		$Commission = round($TotalPremium*17.5/100,2);
		$GSTCommission = round($Commission*0.1,2);
		?>		
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Premium  
				</label>
			</div>
			<div class="controls span6">
				<?php 
					echo  $premiumvalue; 
				?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Administration Fee (Broker Fee)
				</label>
			</div>
			<div class="controls span6">
				<?php 
					echo  $brokerfee; 
				?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Sub Total
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $SubTotal; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					GST Premium
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $GSTPremium; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Stamp Duty
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $StampDuty; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Administration Fee GST
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $GSTfee; ?>		
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Total Premium (includes Commission)
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $TotalPremium ; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Commission
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $Commission; ?>			
			</div>
		</div>
		<div class="control-group">
			<div class="control-label span4">
				<label title="" class="hasTooltip" for="terms" id="terms-lbl">
					Commission GST
				</label>
			</div>
			<div class="controls span6">
				<?php echo  $GSTCommission; ?>			
			</div>
		</div>
		<div class="control-group">
				<div class="control-label"></div>
				<div class="controls">
				<input type="submit" name="next" value="NEXT">
				</div>
	</div>	
	<!-- <input type="hidden" name="premium" value="<?php echo  $premium[0]->brokerfee; ?>"> -->
	<input type="hidden" aria-required="true" value="<?php echo  $premiumvalue; ?>" id="jform_premium" name="jform[premium]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo  $brokerfee; ?>" id="jform_brokerfee" name="jform[brokerfee]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo  $SubTotal; ?>" id="jform_subtotal" name="jform[subtotal]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo  $GSTPremium; ?>" id="jform_gstpremium" name="jform[gstpremium]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo  $StampDuty; ?>" id="jform_stampduty" name="jform[stampduty]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo  $GSTfee; ?>" id="jform_gstfee" name="jform[gstfee]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo  $TotalPremium; ?>" id="jform_totalpremium" name="jform[totalpremium]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo  $Commission; ?>" id="jform_commission" name="jform[commission]" aria-invalid="true">
	<input type="hidden" aria-required="true" value="<?php echo  $GSTCommission; ?>" id="jform_gstcommission" name="jform[gstcommission]" aria-invalid="true">
	<input type="hidden" name="page2" value="page2">
	</form>				
	</div>
	<div class="form-body right span5" >		
		<h3>PERFORMSURE ENDORSEMENTS</h3>		
			
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
				<a href="index.php?option=com_event_manage&view=event" value="Back" class="btn btn-primary" >BACK</a>
			</div>
	</div>
	<div>
		<?php //echo JModuleHelper::renderModule($module); ?>
	</div>

</div>