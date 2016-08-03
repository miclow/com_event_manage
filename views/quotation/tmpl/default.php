<?php 



jimport('joomla.application.module.helper');

JHTML::_('behavior.formvalidation');

JHTML::_('behavior.tooltip');

JHTML::_('behavior.modal');

$module = JModuleHelper::getModule('mod_bt_login');

$loginmodule = JModuleHelper::getModule('mod_loginplus350');



/*$event = $this->event;

$term = $this->term;

$premium = $this->premium;

$Sduty = $this->stampduty;*/

$gstArr = $this->gst;

$premiumArr = $this->premium;

$currencysymbol = '$';

$SdutyArr = $this->stampduty;

$commissionArr = $this->commission;

$perfomers = $this->perfomers;

/*echo $this->state;*/

/*echo '<pre>'; print_r($this->premium); echo '</pre>';*/

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

<div class="page1">
	<ul class="progress-indicator">

		<?php 
		if(!empty($this->sid)){
				$sid = '&sid='.$this->sid;
		} 
		foreach ($this->pageArr as $key => $value) {

			$page = 'page'.$value;
			if(array_key_exists($page, $this->progress->data)){
				if($key != 'quotation'){
					$active = 'completed';
					$color = '#65d074';		
				}
			}else{
				$active = '';		
			}
			if($key == 'quotation'){
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

	<div class="form-title"> <h3> Page 2: Quotation</h3> </div>

	<div class="row-fluid top15">
		<div class="span12" style="margin-left:0";>
			<div class="form-body  span6">			
				<form action="" method="post" enctype="multipart/form-data" id="page2" class="form-validate">
					<div class="row-fluid top15">
						<div class="span12" style="margin-left:0";>
							<h5>PERFORMER DETAILS</h5>
							<div class="control-group">
								<div class="control-label span6">
									<label class="" for="event" id="event-lbl">Name of Insured :</label>
								</div>
								<div class="controls span6">			
									<?php echo $this->frontdata->data['page1']['insurencename']; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid top15">
						<div class="span12" style="margin-left:0";>		
							<div class="control-group">

								<div class="control-label span6">
									<label title="" class="hasTooltip" for="terms" id="terms-lbl">
										Postcode :
									</label>
								</div>
								<div class="controls span6">
									<?php echo  $this->frontdata->data['page1']['postcode']; ?>			
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid top15">
						<div class="control-group">
							<div class="control-label span6">
								<label title="" class="hasTooltip" for="terms" id="terms-lbl">
									Email Address :
								</label>
							</div>
							<div class="controls span6">
								<?php echo  $this->frontdata->data['page1']['email']; ?>			
							</div>
						</div>
					</div>
					<div class="row-fluid top15">
						<div class="control-group">
							<div class="control-label span6">
								<label title="" class="hasTooltip" for="terms" id="terms-lbl">
									Estimated Annual Income :
								</label>
							</div>
							<div class="controls span6">
								<?php echo $this->frontdata->data['page1']['annualincome']; ?>
							</div>
						</div>	
					</div>
					
					<div class="row-fluid top15">
							<div class="control-group" style="margin-top:10px;">
                                 <h5>ACTIVITY DETAILS</h5>
								<div class="control-label span6" style="margin-left:0px;">
									<label class="" for="event" id="event-lbl">Activity :</label>
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
					</div>
					
					<div class="row-fluid top15">
						<div class="span12" style="margin-left:0";>	
							<div class="control-group ">
								<div class="control-label span6">
									<label title="" class="hasTooltip" for="terms" id="terms-lbl">
										Number of Performers :
									</label>
								</div>
								<div class="controls span6">
									<?php 
										//echo $this->frontdata->data['page1']['perfomers'];
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

					<div class="row-fluid top15">
						<div class="span12" style="margin-left:0; margin-top:10px;">
							<h5>COVER DETAILS</h5>
							<div class="control-group">
								<div class="control-label span6">
									<label title="" class="hasTooltip" for="terms" id="terms-lbl">
										Start Date :
									</label>
								</div>
								<div class="controls span6">
									<?php echo  $this->frontdata->data['page1']['start_date']; ?>			
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid top15">
						<div class="control-group span12">
							<div class="control-label span6">
								<label title="" class="hasTooltip" for="terms" id="terms-lbl">
									Public Liability Cover :  
								</label>
							</div>
							<div class="controls span6">
							<?php //echo $this->frontdata->data['page1']['liailitycover']; ?>
								<?php  echo  $currencysymbol.number_format($this->frontdata->data['page1']['liailitycover']); ?>			
							</div>
						</div>
					</div>
				

					<?php 
					if(!empty($this->extrpremium)){
						$Premiumvalue = round($this->extrpremium[0]->price,2);
						$Brokerfee = round($this->extrpremium[1]->price,2);
					}else{
						$Premiumvalue = round($premiumArr[0]->premium,2);
						$Brokerfee = round($premiumArr[0]->brokerfee,2);
					}
					

					$SubTotal = round($Premiumvalue+$Brokerfee,2);

					$GST = '';

					$StampDutyPercentage = '';

					$CommissionFee = '';

					if(!empty($gstArr[0]->price)){

						$GST = str_replace("%","",$gstArr[0]->price);

					}

					if(!empty($SdutyArr[0]->price)){

						$StampDutyPercentage = str_replace("%","",$SdutyArr[0]->price);

					}

					$GSTPremium = round($Premiumvalue*$GST/100,2);

					$GSTfee = round($Brokerfee*$GST/100,2);

					$StampDuty = round($StampDutyPercentage * ($Premiumvalue+$GSTPremium)/100,2);

					$TotalPremium = round($SubTotal+$StampDuty+$GSTPremium+$GSTfee,2);

					if(!empty($commissionArr[0]->price)){

						$CommissionFee = str_replace("%","",$commissionArr[0]->price);

					}

					$Commission = round($Premiumvalue*$CommissionFee/100,2);

					$GSTCommission = round($Commission*$GST/100,2);



					?>		
					<div class="row-fluid top15">
						<div class="span12" style="margin-left:0";>
							<h5>PREMIUM</h5>
							<div class="control-group">
								<div class="control-label span6">
									<label title="" class="hasTooltip" for="terms" id="terms-lbl">
										Base Premium :  
									</label>
								</div>
								<div class="controls span6">
									<?php 
									echo  $currencysymbol.number_format($Premiumvalue,2); 
									?>			
								</div>
							</div>
						</div>
					</div>
					
					<!-- <div class="row-fluid top15">
						<div class="span12" style="margin-left:0";>
							<div class="control-group">
								<div class="control-label span6">
									<label title="" class="hasTooltip" for="terms" id="terms-lbl">
										Sub Total :
									</label>
								</div> -->
								<!-- <div class="controls span6">
									<?php // echo  $currencysymbol.number_format($SubTotal,2); ?>			
								</div>
							</div>
						</div>
					</div> -->
					<div class="row-fluid top15">
						<div class="control-group">
							<div class="control-label span6">
								<label title="" class="hasTooltip" for="terms" id="terms-lbl">
									GST :
								</label>
							</div>
							<div class="controls span6">
								<?php echo  $currencysymbol.number_format($GSTPremium,2); ?>			
							</div>
						</div>
					</div>
					<div class="row-fluid top15">
						<div class="span12" style="margin-left:0";>
							<div class="control-group">
								<div class="control-label span6">
									<label title="" class="hasTooltip" for="terms" id="terms-lbl">
										Stamp Duty :
									</label>
								</div>
								<div class="controls span6">
									<?php echo  $currencysymbol.number_format($StampDuty,2); ?>			
								</div>
							</div>
						</div>
					</div>
                                        <div class="row-fluid top15">
						<div class="control-group">
							<div class="control-label span6">
								<label title="" class="hasTooltip" for="terms" id="terms-lbl">
									Administration Fee :
								</label>
							</div>
							<div class="controls span6">
								<?php 
								echo  $currencysymbol.number_format($Brokerfee,2); 
								?>			
							</div>
						</div>
					</div>
					<div class="row-fluid top15">
						<div class="control-group">
							<div class="control-label span6">
								<label title="" class="hasTooltip" for="terms" id="terms-lbl">

									Administration Fee GST :
								</label>
							</div>
							<div class="controls span6">
								<?php echo  $currencysymbol.number_format($GSTfee,2); ?>		
							</div>
						</div>
					</div>
					
					<div class="row-fluid top15">
						<div class="span12" style="margin-left:0";>
							<div class="control-group ">
								<div class="control-label span6">
									<label title="" class="hasTooltip" for="terms" id="terms-lbl">
										<strong>TOTAL PREMIUM :</strong>
									</label>
								</div>
								<div class="controls span6">
									<strong><?php echo  $currencysymbol.number_format($TotalPremium,2) ; ?></strong>
								</div>
							</div>
						</div>
					</div>
					<!-- <div class="row-fluid top15">
						<div class="control-group">
							<div class="control-label span6">
								<label title="" class="hasTooltip" for="terms" id="terms-lbl">

									Commission :
								</label>
							</div>
							<div class="controls span6">

								<?php echo  $currencysymbol.$Commission; ?>			
							</div>
						</div>
					</div>
					<div class="row-fluid top15">
						<div class="span12" style="margin-left:0";>
							<div class="control-group ">
								<div class="control-label span6">
									<label title="" class="hasTooltip" for="terms" id="terms-lbl">
										Commission GST :
									</label>
								</div>
								<div class="controls span6">
									<?php echo  $currencysymbol.$GSTCommission; ?>			
								</div>
							</div>
						</div>
					</div> -->
					<!-- <input type="hidden" name="premium" value="<?php echo  $premium[0]->brokerfee; ?>"> -->

					<input type="hidden" aria-required="true" value="<?php echo  $Premiumvalue; ?>" id="jform_premium" name="jform[premium]" aria-invalid="true">

					<input type="hidden" aria-required="true" value="<?php echo  $Brokerfee; ?>" id="jform_brokerfee" name="jform[brokerfee]" aria-invalid="true">

					<input type="hidden" aria-required="true" value="<?php echo  $SubTotal; ?>" id="jform_subtotal" name="jform[subtotal]" aria-invalid="true">

					<input type="hidden" aria-required="true" value="<?php echo  $GSTPremium; ?>" id="jform_gstpremium" name="jform[gstpremium]" aria-invalid="true">

					<input type="hidden" aria-required="true" value="<?php echo  $StampDuty; ?>" id="jform_stampduty" name="jform[stampduty]" aria-invalid="true">

					<input type="hidden" aria-required="true" value="<?php echo  $GSTfee; ?>" id="jform_gstfee" name="jform[gstfee]" aria-invalid="true">

					<input type="hidden" aria-required="true" value="<?php echo  $TotalPremium; ?>" id="jform_totalpremium" name="jform[totalpremium]" aria-invalid="true">

					<input type="hidden" aria-required="true" value="<?php echo  $Commission; ?>" id="jform_commission" name="jform[commission]" aria-invalid="true">

					<input type="hidden" aria-required="true" value="<?php echo  $GSTCommission; ?>" id="jform_gstcommission" name="jform[gstcommission]" aria-invalid="true">

					<div class="control-group pull-left MyMargin">
						<div class="control-label"></div>
						<div class="controls">
							<?php 
								if($this->sid) {
									$sid = '&sid='.$this->sid;
								}
							?>
							<a href="index.php?option=com_event_manage&view=event&step=back<?php echo $sid; ?>" value="Back" class="btn btn-primary" >BACK</a>
						</div>
					</div>
					<div class="control-group pull-left MyMargin">
						<div class="control-label"></div>
						<div class="controls">
							<input type="submit" class="btn btn-primary" name="next" value="NEXT">
						</div>
					</div>

					<div class="control-group pull-left MyMargin">
						<div class="controls">
							<?php if($this->useremail) { ?>
							<input type="button" class="btn btn-primary" id="SaveComeBackLeter" name="Savedata" value="Save & Exit">
							<?php } else { ?>
							<a id="create-user" href="#openModalEmail">
								<input type="button" class="btn btn-primary" id="SaveComeBack" name="Savedata" value="Save & Exit">
							</a>
							<?php }?>

						</div>
					</div>
					<input type="hidden" name="page2" value="page2">
				</form>				
			</div>
			<div class="form-body span6" >				
				<div class="control-group">			
					<div class="controls">
						<h3>Exclusions & Endorsements</h3>
						{article 27}[text]{/article}
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">

		js = jQuery.noConflict();

		js(document).ready(function () {

			js("#SaveComeBack").click(function(){
				var email = "<?php echo $this->frontdata->data['page1']['email']; ?>";
				js("#btl-input-user-check-email").val(email);
			});

			js("#SaveComeBackLeter").click(function(){
					js("#btl-check-user-error").hide();

					js(".btl-error-detail").hide();

					var email = js("#btl-input-user-check-email").val();
					var sid   = '<?php echo $sid; ?>';
					var emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;

					if(!emailRegExp.test(js("#btl-input-user-check-email").val())){		

						js("#btl-check-user-error").html(btlOpt.MESSAGES.EMAIL_INVALID).show();

						js("#btl-input-user-check-email").focus().select();

						return false;

					}

					var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=quotation&step=2&status=notfound');?>";
					var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=quotation&step=2&status=found');?>";
					var datasubmit = js('#btl-check-user form').serialize();
					console.log(datasubmit);				

					js.ajax({

						type: "POST",
						beforeSend:function(){
							js("#btl-check-user-in-process").show();			   

						},
						url: 'index.php?option=com_event_manage&task=checkUserName',
						data: datasubmit,

						success: function(html){	

							obj = JSON.parse(html);		

							if(obj.status == 'found'){	
								if(js('.btl-modal').length){
									//alert("1");
									//alert("yes");
									window.location.href = loginurl+'&amp;email1='+email+sid;
								}else{

									//alert("2");
									/*alert("yes");*/
									window.location.href = loginurl+'&amp;email1='+email+sid;
								}
							}else{

								window.location.href = redirecturl+'&amp;email1='+email+sid;
								/*alert("no");*/
							}
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							alert(textStatus + ': Ajax request failed');
						}
					});
					return false;
			});

			js("#btl-buttonsubmit").click(function(){

				js("#btl-check-user-error").hide();

				js(".btl-error-detail").hide();

				var email = js("#btl-input-user-check-email").val();

				var emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;

				if(!emailRegExp.test(js("#btl-input-user-check-email").val())){		

					js("#btl-check-user-error").html(btlOpt.MESSAGES.EMAIL_INVALID).show();

					js("#btl-input-user-check-email").focus().select();

					return false;

				}

				var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=quotation&step=2&status=notfound');?>";
				var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=quotation&step=2&status=found');?>";
				var datasubmit = js('#btl-check-user form').serialize();
				console.log(datasubmit);				

				js.ajax({

					type: "POST",
					beforeSend:function(){
						js("#btl-check-user-in-process").show();			   

					},
					url: 'index.php?option=com_event_manage&task=checkUserName',
					data: datasubmit,

					success: function(html){	

						obj = JSON.parse(html);		

						if(obj.status == 'found'){	
							if(js('.btl-modal').length){
							//alert("1");
							//alert("yes");
							window.location.href = loginurl+'&amp;email1='+email;
							}else{

								//alert("2");
								//alert("yes");
								window.location.href = loginurl+'&amp;email1='+email;
							}
						}else{

							window.location.href = redirecturl+'&amp;email1='+email;
				   			//alert("no");
				   		}
				   	},
				   	error: function (XMLHttpRequest, textStatus, errorThrown) {
				   		alert(textStatus + ': Ajax request failed');
				   	}
				});
				return false;
			});

			var a = "<?php echo $this->newuserid; ?>";
			if(a){

				/*alert('Yes return sucess');*/
				var input = js("<input>").attr("type", "hidden").attr("name", "jform[userid]").val(a);
				js('#page2').append(js(input));
				js("#page2").submit();
			}else{
				/*alert(a);
				alert('no');*/
			}
		});

	</script>

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
							<?php if($this->useremail) { ?>
							<input type="email" name="email1" id="btl-input-user-check-email" value="<?php echo $this->useremail; ?>" class="validate-email" aria-invalid="false">
							<?php } else { ?>
							Email: <input id="btl-input-user-check-email" type="email" name="email1" />
							<?php } ?>
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

	<div id="openModalLogin" class="modalDialog">
		<div class="inner-div">
			<a href="#close" title="Close" class="close">X</a>
	        <!-- <div id="btl-check-user" class="btl-content-block">
				<?php //echo JModuleHelper::renderModule($loginmodule); ?>
			</div> -->
			<div class="well">
				<?php echo JModuleHelper::renderModule($loginmodule); ?>
			</div>	
		</div>
	</div>
</div>