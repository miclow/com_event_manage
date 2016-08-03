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
	<div class="form-title"> <h1>Checkout Page 6</h1> </div>
	<div class="form-body left span9">			
		<form action="" method="post" enctype="multipart/form-data" id="page6" class="form-validate">
			<div class="control-group">
				<div class="control-label span3">
					<label class="" for="event" id="event-lbl">Start Date</label>
				</div>
				<div class="controls">			
					<?php echo $this->frontdata->data['page1']['start_date']; ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label class="" for="event" id="event-lbl">Public Liability Cover</label>
				</div>
				<div class="controls">			
					<?php 
					echo  $this->frontdata->data['page1']['liailitycover']; 
					?>
				</div>
			</div>	
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Extra Charges
					</label>
				</div>
				<?php 
				if(!empty($Extrachreges)){
					$a=0;
					foreach ($Extrachreges as $evalue) {
							# code...
						$charges += $evalue->price;
						echo '<div class="controls">';
						echo $evalue->name.' '.$evalue->price;
						echo '</div>';
						echo '<input type="hidden" value="'.$evalue->price.'"name="jform[extrachreges]['.$evalue->name.']" id="jform_extrachreges'.$a.'"> ';
						$a++;
					}
				}

				if(!empty($ChregesByOrderId)){
					$c=$a;
					foreach ($ChregesByOrderId as $cvalue) {
							# code...
						$charges += $cvalue->price;
						echo '<div class="controls">';
						echo $cvalue->name.' '.$cvalue->price;
						echo '</div>';
						echo '<input type="hidden" value="'.$cvalue->price.'"name="jform[extrachreges]['.$cvalue->name.']" id="jform_extrachreges'.$c.'"> ';
						$c++;
					}
				}
					//echo $this->frontdata->data['page1']['perfomers']; 
				?>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Order Total Preview
					</label>
				</div>
				<div class="controls">&nbsp;</div>
			</div><br>		
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Total Premium
					</label>
				</div>
				<div class="controls">
					<?php echo  $TotalPremium; ?>			
				</div>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Total Extras
					</label>
				</div>
				<div class="controls">
					<?php 
					echo $TotalExtras;

					?>			
				</div>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Subtotal
					</label>
				</div>
				<div class="controls">
					<?php echo $Subtotal; ?>			
				</div>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Credit Card Fee 
					</label>
				</div>
				<div class="controls">
					<?php echo  $CreditCardFee; ?>			
				</div>
			</div>
			<div class="control-group">
				<div class="control-label span3">
					<label title="" class="hasTooltip" for="terms" id="terms-lbl">
						Order Total  
					</label>
				</div>
				<div class="controls">
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
					<div class="controls">
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
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('CVV'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('CVV'); ?></div>
			</div>
			<div class="control-group pull-left MyMargin">
				<div class="controls">
					<?php 
						if($this->sid) {
							$sid = '&sid='.$this->sid;
						}
					?>
					<a href="index.php?option=com_event_manage&view=declaration&step=5<?php echo $sid; ?>" value="Back" class="btn btn-primary" >BACK</a>
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