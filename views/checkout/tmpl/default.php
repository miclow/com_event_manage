<?php 

jimport('joomla.application.module.helper');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
$module = JModuleHelper::getModule('mod_bt_login');

/*echo $this->state;*/
/*echo '<pre>'; print_r($this->stampduty); echo '</pre>';*/
$charges = 0;
if(!empty($this->Extracharegs)){
	$Extrachreges = $this->Extracharegs;
	foreach ($Extrachreges as $evalue) {
		$charges += $evalue->price;
	}
}

if(!empty($this->ExtraCharegsByOrderId)){
	$ChregesByOrderId = $this->ExtraCharegsByOrderId;
	foreach ($ChregesByOrderId as $cvalue) {
		$charges += $cvalue->price;
	}
}

$CardFee = $this->CreditCardFee;
$StartDate = $this->frontdata->data['page1']['start_date'];
$PublicLiabilityCover = $this->frontdata->data['page1']['liailitycover'];
$TotalPremium = $this->frontdata->data['page2']['totalpremium'];
$TotalExtras = round($charges,2);
$Subtotal = round($TotalPremium+$TotalExtras, 2);
$CreditCardFee = round($Subtotal*$CardFee[0]->price/100 , 2);
$OrderTotal  = round($Subtotal+$CreditCardFee,2);
$perfomers = $this->perfomers;

$url = JUri::base().'administrator/components/com_event_manage/assets/css/progress-wizard.min.css';
$document = JFactory::getDocument();
$document->addStyleSheet($url);
$currencysymbol = '$';

$this->progress->data = unserialize($this->progress->data);
?>

<?php
if(isset($this->errors) && $this->errors != ''){ ?>
<div class="error-class">
	<?php echo $this->errors; ?>
</div>

<?php } ?> 
<style type="text/css">
	#jform_month,#jform_year{
		width: 80px !important;
	}
</style>
<script type="text/javascript">
		js = jQuery.noConflict();
		js(document).ready(function () {
			js('#submit_need_assistance, #SaveComeBack').click(function (event){
				// event.preventDefault(); 
				js(".select.inputbox").removeAttr("required");
				js(".select.inputbox").removeAttr("aria-required");
				// js(this).parents("form").submit();
			
			});

			js("#SaveComeBack").click(function(){

				var email1 = js("#jform_confirmemail").val();

	    		if(email1 != "" ){

		    		var emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;

					var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=declaration&step=5&status=notfound');?>";
					var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=declaration&step=5&status=found');?>";
					var datasubmit = js("#jform_confirmemail").serialize();
					console.log(datasubmit);				

					js.ajax({

					   type: "POST",
					   beforeSend:function(){
						   js("#btl-check-user-in-process").show();			   
					   },

					   url: 'index.php?option=com_event_manage&task=checkUserNameByEmail',

					   data: datasubmit,

					   success: function(html){	
					   		obj = JSON.parse(html);		
					   		if(obj.status == 'found'){	
					   			if(js('.btl-modal').length){
									var input = js("<input>").attr("type", "hidden").attr("name", "jform[status]").val('found');
									js('#page6').append(js(input));
									js("#submit").click();

								}else{

								var input = js("<input>").attr("type", "hidden").attr("name", "jform[status]").val('found');
									js('#page6').append(js(input));
									js("#submit").click();
								}
					   		}else{
					   			var input = js("<input>").attr("type", "hidden").attr("name", "jform[status]").val('notfound');
								js('#page6').append(js(input));
								js("#submit").click();
					   		}
					   },

					   error: function (XMLHttpRequest, textStatus, errorThrown) {
							alert(textStatus + ': Ajax request failed');
					   }
					});
					return false;
	    		}
			
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
				if($key != 'checkout'){
					$active = 'completed';
					$color = '#65d074';			
				}
			}else{
				$active = '';		
			}
			if($key == 'checkout'){
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
	<div class="form-title"> <h3>Page 6: Checkout</h3> </div>
	<div class="form-body left span9">			
		<form action="" method="post" enctype="multipart/form-data" id="page6" class="form-validate">
			<!-- Activity Details Start here -->
			<h5>ACTIVITY DETAILS</h5>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">
					<div class="control-group">
						<div class="control-label span6">
							<label class="" for="event" id="event-lbl">Activity:</label>
						</div>
						<div class="controls span6">			
							<?php 
								$activityData = $this->frontdata->data['page1']['activity']; 
								$acdata = '';
								foreach($activityData as $activdata){

									$acdata .= $activdata.', ';
								}
								echo substr($acdata,0,-2);
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">
					<div class="control-group">
						<div class="control-label span6">
							<label class="" for="event" id="event-lbl">Number of Performers:</label>
						</div>
						<div class="controls span6">			
							<?php 
								foreach ($perfomers as $pvalue) {
									// $pchek = '';
									if(!empty($this->frontdata->data['page1']['perfomers'])){
										if($pvalue->price == $this->frontdata->data['page1']['perfomers']){
											echo $pvalue->name;
										}
									}else{
										echo '--';
									}
								}
							?>
						</div>
					</div>	
				</div>
			</div>	
			<div>&nbsp;</div>	
			<!-- Activity Details End here -->
			<!-- Cover Details Start here -->
			<h5>COVER DETAILS</h5>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">
					<div class="control-group">
						<div class="control-label span6">
							<label class="" for="event" id="event-lbl">Start Date:</label>
						</div>
						<div class="controls span6">			
							<?php echo date("d-m-Y",strtotime($this->frontdata->data['page1']['start_date'])); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">
					<div class="control-group">
						<div class="control-label span6">
							<label class="" for="event" id="event-lbl">Public Liability Cover:</label>
						</div>
						<div class="controls span6">			
							<?php 
							if (strpos($this->frontdata->data['page1']['liailitycover'], '$') !== false) {
							    echo $this->frontdata->data['page1']['liailitycover']; 
							}else{
								echo  $currencysymbol.number_format($this->frontdata->data['page1']['liailitycover']); 
							}
							?>
						</div>
					</div>	
				</div>
			</div>
			<div>&nbsp;</div>	
			<!-- Cover Details End here -->
			<!-- Premium Details Start here -->
			<h5>PREMIUM</h5>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								Base Premium:
							</label>
						</div>
						<div class="controls span6">
							<?php echo $currencysymbol.number_format($this->frontdata->data['page2']['premium'],2); ?>			
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								GST:
							</label>
						</div>
						<div class="controls span6">
							<?php echo $currencysymbol.number_format($this->frontdata->data['page2']['gstpremium'],2); ?>			
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								Stamp Duty:
							</label>
						</div>
						<div class="controls span6">
							<?php echo $currencysymbol.number_format($this->frontdata->data['page2']['stampduty'],2); ?>			
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								Administration Fee:
							</label>
						</div>
						<div class="controls span6">
							<?php echo $currencysymbol.number_format($this->frontdata->data['page2']['brokerfee'],2); ?>			
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								Administration Fee GST:
							</label>
						</div>
						<div class="controls span6">
							<?php echo $currencysymbol.number_format($this->frontdata->data['page2']['gstfee'],2); ?>			
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								<strong>PREMIUM:</strong>
							</label>
						</div>
						<div class="controls span6">
							<strong><?php echo  $currencysymbol.number_format($TotalPremium,2); ?></strong>			
						</div>
					</div>
				</div>
			</div>
			<div>&nbsp;</div>
			<div>&nbsp;</div>
			<h5>Extra Charges</h5>
			<?php 
						
						if(!empty($Extrachreges)){
							
								$a=0;
								foreach ($Extrachreges as $evalue) {
										# code...
									$charges += $evalue->price;
									echo '<div class="row-fluid top15">
									<div class="span12" style="margin-left:0;">
										<div class="control-group">';
									echo '<div class="control-label span6">
											<label title="" class="hasTooltip" for="terms" id="terms-lbl">'.$evalue->name.':</label>
										  </div>';
									echo '<div class="controls span6">$'.number_format($evalue->price,2).'</div><br/>';
									echo '<input type="hidden" value="'.$evalue->price.'"name="jform[extrachreges]['.$evalue->name.']" id="jform_extrachreges'.$a.'"> ';
									echo '</div>
									 </div>
								   </div>';
									$a++;
								}
							
						}
						if(!empty($ChregesByOrderId)){
							
								$c=$a;
								foreach ($ChregesByOrderId as $cvalue) {
										
									$charges += $cvalue->price;
									echo '<div class="row-fluid top15">
									<div class="span12" style="margin-left:0;">
										<div class="control-group">';
									echo '<div class="control-label span6">
											<label title="" class="hasTooltip" for="terms" id="terms-lbl">'.$cvalue->name.':</label>
										  </div>';
									echo '<div class="controls span6">$'.number_format($cvalue->price,2).'</div><br/>';
									echo '<input type="hidden" value="'.$cvalue->price.'"name="jform[extrachreges]['.$cvalue->name.']" id="jform_extrachreges'.$c.'"> ';
									echo '</div>
									 </div>
								   </div>';
									$c++;
								}
							
						}
							//echo $this->frontdata->data['page1']['perfomers']; 
						?>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								<strong>TOTAL EXTRAS:</strong>
							</label>
						</div>
						<div class="controls span6">
							<strong><?php 
							echo $currencysymbol.number_format($TotalExtras,2);

							?></strong>			
						</div>
					</div>
				</div>
			</div>
			<div></div>		
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								<strong>TOTAL PREMIUM:</strong>
							</label>
						</div>
						<div class="controls span6">
							<strong><?php echo $currencysymbol.number_format($Subtotal,2); ?></strong>			
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								Credit Card Fee: 
							</label>
						</div>
						<div class="controls span6">
							<?php echo  $currencysymbol.number_format($CreditCardFee,2); ?>			
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							<label title="" class="hasTooltip" for="terms" id="terms-lbl">
								<strong>ORDER TOTAL:</strong>  
							</label>
						</div>
						<div class="controls span6">
							<strong><?php echo  $currencysymbol.number_format($OrderTotal,2); ?></strong>			
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6"><?php echo $this->form->getLabel('creditcard'); ?></div>
						<div class="controls span6"><?php echo $this->form->getInput('creditcard'); ?></div>
					</div>
				</div>
			</div>
			<!-- Premium Details End here -->
			<!-- Credit Card Details Start here -->
			<h5>CREDIT CARD DETAILS</h5>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6">
							Your billing information must match the billing address for the credit card entered below or we will be unable to process your payment
						</div>
					</div>
				</div>
			</div>
			<div>&nbsp;</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6"><?php echo $this->form->getLabel('nameoncard'); ?></div>
						<div class="controls span6"><?php echo $this->form->getInput('nameoncard'); ?></div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6"><?php echo $this->form->getLabel('cardnumber'); ?></div>
						<div class="controls span6"><?php echo $this->form->getInput('cardnumber'); ?></div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">	
							<div class="control-label span6"><?php echo $this->form->getLabel('expirationdate'); ?></div>
							<div class="controls span6">
								<?php echo $this->form->getInput('month'); echo '/';?>
								<?php

									 echo '<select aria-required="true" class="inputbox select required" name="jform[year]" id="jform_year" aria-invalid="false">';
									 $starting_year  =date('y');
									 $ending_year = date('y', strtotime('+24 year'));
									 $current_year = date('y');
									 echo '<option value="">Year</option>';
									 for($starting_year; $starting_year <= $ending_year; $starting_year++) {
									     echo '<option value="'.$starting_year.'"';
									     echo ' >'.$starting_year.'</option>';
									 }               
									 echo '</select>';
								?>
							</div>
					</div>
				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">		
					<div class="control-group">
						<div class="control-label span6"><?php echo $this->form->getLabel('CVV'); ?></div>
						<div class="controls span6"><?php echo $this->form->getInput('CVV'); ?></div>
					</div>
				</div>
			</div>
			<!-- Credit Card Details End here -->
			<div class="control-group pull-left MyMargin">
				<div class="controls">
					<?php 
						if($this->sid) {
							$sid = '&sid='.$this->sid;
						}
					?>
					<a href="index.php?option=com_event_manage&view=declaration&step=5<?php echo $sid; ?>" value="Back" class="btn btn-primary" id="back-pag5">BACK</a>
				</div>
			</div>
			<div class="control-group pull-left MyMargin">
				<div class="controls">
					<input type="submit" class="btn btn-primary" id="submit" name="submit" value="NEXT">
				</div>
			</div>
			<div class="control-group pull-left MyMargin">
				<div class="controls">
					<input type="Submit" class="btn btn-primary" id="submit_need_assistance" name="submit_need_assistance" value="I NEED ASSISTANCE">
				</div>
			</div>
			<div class="control-group pull-left MyMargin">
				<div class="controls">
					<input type="button" class="btn btn-primary" id="SaveComeBack" name="Savedata" value="Save & Come back Later">
				</div>
			</div>
			<input type="hidden" aria-required="true" value="<?php echo $StartDate; ?>" id="jform_premium" name="jform[startdate]" aria-invalid="true">
			<input type="hidden" aria-required="true" value="<?php echo $PublicLiabilityCover; ?>" id="jform_publicliailitycover" name="jform[publicliailitycover]" aria-invalid="true">
			<input type="hidden" aria-required="true" value="<?php echo $TotalPremium; ?>" id="jform_totalpremium" name="jform[totalpremium]" aria-invalid="true">
			<input type="hidden" aria-required="true" value="<?php echo $Subtotal; ?>" id="jform_subtotal" name="jform[subtotal]" aria-invalid="true">
			<input type="hidden" aria-required="true" value="<?php echo $OrderTotal; ?>" id="jform_orderTotal" name="jform[orderTotal]" aria-invalid="true">
			<input type="hidden" aria-required="true" value="<?php echo $CreditCardFee; ?>" id="jform_creditcardfee" name="jform[creditcardfee]" aria-invalid="true">
			<input type="hidden" name="page6" value="page6">
		</form>			
	</div>
	
	<div>
		<?php //echo JModuleHelper::renderModule($module); ?>
	</div>
	<div id="openModalEmail" class="modalDialog">
        <div class="inner-div">
            <a href="#close" title="Close" class="close">X</a>
            <div id="btl-check-user" class="btl-content-block">
				<form name="btl-formCheckUser" class="btl-formlogin" action="" method="post">
					<div id="btl-check-user-in-process"></div>	
					<h3><?php echo JText::_('COM_EVENT_MANAGE_ENTER_EMAIL_ADDRESS') ?></h3>
					
					<div class="spacer"></div>
					
					<div class="btl-error" id="btl-check-user-error"></div>
					<div class="btl-field">
						
						<div class="btl-input">
							<!-- Email: <input id="btl-input-user-check-email" type="text" value="<?php echo $this->useremail; ?>" name="jform[confirmemail]" /> -->
							<input type="email" name="jform[confirmemail]" id="jform_confirmemail" value="<?php echo $this->useremail; ?>" class="validate-email" aria-invalid="false">
						</div>
					</div>
					<div class="btl-buttonsubmit">						
						<button id="btl-buttonsubmit" class="btn btn-primary" onclick="return checkUserAjax()" >
							<?php echo JText::_('CHECK_USER');?>							
						</button>
						<input type="hidden" name="type" value="checkUserName" />  					
						<?php echo JHtml::_('form.token');?>
					</div>
				</form>
			</div>
        </div>
    </div>

</div>
<style type="text/css">
	
			.modalDialog {
                position: fixed;
                top: 0;
                right: 0;
                /*bottom: 0;*/
                left: 0;
                background: rgba(0,0,0,0.8);
                z-index: 99999;
                opacity:0;
                -webkit-transition: opacity 400ms ease-in;
                -moz-transition: opacity 400ms ease-in;
                transition: opacity 400ms ease-in;
                pointer-events: none;
                height: 100%;
              }
              .modalDialog h2 { float:left; margin:0 auto; }
              .modalDialog tr { height:35px;}

              .modalDialog:target {
                opacity:1;
                pointer-events: auto;
              }

              .modalDialog > div {
                width: 25%;
                height:96%; 
                position: relative;
                margin: 10% auto;
                margin-top: 30px;
                padding: 0px;
                border-radius: 10px;
                background: #fff;
                background: -moz-linear-gradient(#fff, #e5e5e5);
                background: -webkit-linear-gradient(#fff, #e5e5e5);
                background: -o-linear-gradient(#fff, #e5e5e5);
              }

              .close {
                background: #308194;
                color: #FFFFFF !important;
                line-height: 25px;
                position: absolute;
                right: -10px;
                text-align: center;
                top: -10px;
                width: 24px;
                text-decoration: none;
                font-weight: bold;
                -webkit-border-radius: 12px;
                -moz-border-radius: 12px;
                border-radius: 12px;
                -moz-box-shadow: 1px 1px 3px #000;
                -webkit-box-shadow: 1px 1px 3px #000;
                box-shadow: 1px 1px 3px #000;
                z-index:10;
              }

              .close:hover { background: #00d9ff; color: #ccc !important;  }
              .inner-div{
              	height: auto !important;
              	padding: 20px !important;
              	background:#fff !important;
              }
</style>