<?php 



jimport('joomla.application.module.helper');

JHTML::_('behavior.formvalidation');

JHTML::_('behavior.tooltip');

//$module = JModuleHelper::getModule('mod_bt_login');

$loginmodule = JModuleHelper::getModule('mod_loginplus350');



/*$premium = $this->premium;*/

//echo '<pre>'; print_r($event); print_r($term); echo '</pre>';

$url = JUri::base().'administrator/components/com_event_manage/assets/css/progress-wizard.min.css';

$document = JFactory::getDocument();

$document->addStyleSheet($url);



$this->progress->data = unserialize($this->progress->data);
// echo "<pre>";
// print_r($this->progress->data);
// echo "</pre>";

$getstateid = $this->form->getValue('state');
$getbillingstateid = $this->form->getValue('billingstate');

?>



<?php

if(isset($this->errors) && $this->errors != ''){ ?>

<div class="error-class">

	<?php 

	JFactory::getApplication()->enqueueMessage($this->errors,'error');

	?>

</div>



<?php } ?> 

<script type="text/javascript">

	js = jQuery.noConflict();

	js(document).ready(function () {

		js("#jform_sameaddress0").change(function(){

			if(js(this).is(":checked")){



				js("#jform_billingaddress1").val(js("#jform_streetaddress1").val());

				js("#jform_billingaddress2").val(js("#jform_streetaddress2").val());

				js("#jform_billingsuburb").val(js("#jform_suburb").val());

				js("#jform_billingstate").val(js("#jform_state").val());

				js("#jform_billingpostcode").val(js("#jform_postcode").val());

			}else{

				js("#jform_billingaddress1").val('');

				js("#jform_billingaddress2").val('');

				js("#jform_billingsuburb").val('');

				js("#jform_billingstate").val(js("#jform_billingstate option:first").val());

				js("#jform_billingpostcode").val('');
			}
		});

		js("#SaveComeBack").click(function(){

				// var email1 = js("#jform_email").val();

				var email2 = js("#jform_confirmemail").val();

						var emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;

						if(!emailRegExp.test(email2)){		

							js("#jform_confirmemail").html(btlOpt.MESSAGES.EMAIL_INVALID).show();

							js("#jform_confirmemail").focus().select();

							return false;

						}

						var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=address&step=4&status=notfound');?>";

						var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=address&step=4&status=found');?>";

						var datasubmit = js("#jform_confirmemail").serialize();

						console.log(datasubmit);				

						/*alert(datasubmit);*/

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

										/*window.location.href = loginurl;*/

										var input = js("<input>").attr("type", "hidden").attr("name", "jform[status]").val('found');

										js('#page4').append(js(input));

										js("#submit").click();

									}else{

										 // alert("yes");

										/*window.location.href = loginurl;*/

										var input = js("<input>").attr("type", "hidden").attr("name", "jform[status]").val('found');

										js('#page4').append(js(input));

										js("#submit").click();

									}

								}else{

									// alert("no");

									var input = js("<input>").attr("type", "hidden").attr("name", "jform[status]").val('notfound');

									js('#page4').append(js(input));

									js("#submit").click();

					   			//window.location.href = redirecturl;	
					   		}
					   	},

					   	error: function (XMLHttpRequest, textStatus, errorThrown) {
					   		// alert(textStatus + ': Ajax request failed');
					   	}

					   });

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
				if($key != 'address'){
					$active = 'completed';
					$color = '#65d074';		
				}
			}else{
				$active = '';		
			}

			if($key == 'address'){
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

	<div class="form-title"> <h3>Page 4: Name and Address</h3> </div>

	<div class="form-body">			

		<form action="" method="post" enctype="multipart/form-data" id="page4" class="form-validate">

			
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0";>	
					<div class="control-group">

						<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>

						<div class="controls"><?php echo $this->form->getInput('id'); ?></div>

					</div>

					<div class="control-group">

						<div class="control-label"><?php //echo $this->form->getLabel('created_by'); ?></div>

						<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>

					</div>
				</div>
			</div>

			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0;">	

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6"><?php echo $this->form->getLabel('firstname'); ?></div>

						<div class="controls span6"><?php echo $this->form->getInput('firstname'); ?></div>

					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6"><?php echo $this->form->getLabel('lastname'); ?></div>

						<div class="controls span6"><?php echo $this->form->getInput('lastname'); ?></div>

					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6"><?php echo $this->form->getLabel('insuredname'); ?></div>

						<div class="controls span6">
								<?php //echo $this->form->getInput('insuredname'); ?>
								<input type="text" id="jform_insuredname" required="true" name="jform[insuredname]" value="<?php echo $this->progress->data['page1']['insurencename'];?>" aria-invalid="false">
						</div>

					</div>
				</div>
			</div>

			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0";>	


					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6" ><?php echo $this->form->getLabel('streetaddress1'); ?></div>

						<div class="controls span6" ><?php echo $this->form->getInput('streetaddress1'); ?></div>

					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6" ><?php echo $this->form->getLabel('streetaddress2'); ?></div>

						<div class="controls span6" ><?php echo $this->form->getInput('streetaddress2'); ?></div>

					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6"><?php echo $this->form->getLabel('suburb'); ?></div>

						<div class="controls span6"><?php echo $this->form->getInput('suburb'); ?></div>

					</div>
				</div>
			</div>

			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0";>	

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6"><?php echo $this->form->getLabel('state'); ?></div>

						<div class="controls span6" >
							<?php //echo $this->form->getInput('state'); ?>
							<select name="jform[state]" id="jform_state" aria-invalid="false">
								<option value="">Please Select</option>
								<?php 
								/*echo $this->form->getInput('perfomers'); */
								// print_r($this->statename);
								foreach ($this->statename as $svalue) {
									$pchek = '';
									// echo $svalue;
									if(!empty($getstateid)){
										if($svalue->id == $getstateid){
											$pchek = 'selected="selected"';
										}else{
											$pchek = '';
										}
									}

									echo '<option value="'.$svalue->id.'" '.$pchek.'>'.$svalue->name.'</option>';
								}
								?>
							</select>
						</div>

					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6" ><?php echo $this->form->getLabel('postcode'); ?></div>

						<div class="controls span6" ><?php echo $this->form->getInput('postcode'); ?></div>

					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6"><?php echo $this->form->getLabel('phone'); ?></div>

						<div class="controls span6"><?php echo $this->form->getInput('phone'); ?></div>

					</div>
				</div>
			</div>

			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0";>	

					<div class="control-group span8" style="margin-left:0;">
						<div class="control-label"><?php echo $this->form->getLabel('sameaddress'); ?></div>
						<?php 
							$chek = '';
							if($this->progress->data['page4']['sameaddress'][0] == 'Y'){
								$chek = 'checked="checked"';
							}
						?>
						<input type="checkbox" id="jform_sameaddress0" name="jform[sameaddress][]" value="Y" <?php echo $chek;?> > Yes
					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6"><?php echo $this->form->getLabel('billingaddress1'); ?></div>

						<div class="controls span6"><?php echo $this->form->getInput('billingaddress1'); ?></div>

					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6" ><?php echo $this->form->getLabel('billingaddress2'); ?></div>

						<div class="controls span6"><?php echo $this->form->getInput('billingaddress2'); ?></div>

					</div>
				</div>
			</div>

			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0";>	

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6"><?php echo $this->form->getLabel('billingsuburb'); ?></div>

						<div class="controls span6" ><?php echo $this->form->getInput('billingsuburb'); ?></div>

					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6"><?php echo $this->form->getLabel('billingstate'); ?></div>

						<div class="controls span6">
							<?php //echo $this->form->getInput('billingstate'); ?>
							<select name="jform[billingstate]" id="jform_billingstate" aria-invalid="false">
								<option value="">Please Select</option>
								<?php 
								foreach ($this->statename as $bvalue) {
									$bchek = '';
									// echo $svalue;
									if(!empty($getbillingstateid)){
										if($bvalue->id == $getbillingstateid){
											$bchek = 'selected="selected"';
										}else{
											$bchek = '';
										}
									}

									echo '<option value="'.$bvalue->id.'" '.$bchek.'>'.$bvalue->name.'</option>';
								}
								?>
							</select>
						</div>

					</div>

					<div class="control-group span8" style="margin-left:0;">

						<div class="control-label span6" ><?php echo $this->form->getLabel('billingpostcode'); ?></div>

						<div class="controls span6" ><?php echo $this->form->getInput('billingpostcode'); ?></div>

					</div>

				</div>
			</div>
			<div class="row-fluid top15">
				<div class="span12" style="margin-left:0";>
					<div class="control-group span4">
						<input type="hidden" name="jform[confirmemail]" id="jform_confirmemail" value="<?php echo $this->progress->data['page1']['email'];?>" class="" aria-invalid="false">
					</div>
				</div>
			</div>

			<div class="control-group pull-left MyMargin">

				<div class="control-label ">				

				</div>

				<div class="controls ">
					<?php 
					if($this->sid) {
						$sid = '&sid='.$this->sid;
					}
					?>
					<a href="index.php?option=com_event_manage&view=proposal&step=3<?php echo $sid; ?>" value="Back" class="btn btn-primary" >BACK</a>

				</div>

			</div>
			<div class="control-group pull-left MyMargin">

				<div class="control-label"></div>

				<div class="controls "><?php echo $this->form->getInput('next'); ?></div>
				<input type="hidden" name="jform[sid]" id="jform_sid" value="<?php echo $this->sid; ?>">
				<input type="hidden" name="page4" value="page4">

			</div>	
			<div class="control-group pull-left MyMargin">

				<div class="controls ">

					<input type="button" class="btn btn-primary" name="Savedata" id="SaveComeBack" value="Save & Exit">
				</div>

			</div>

			<div>
			</form>				

		</div>

		<?php //echo JModuleHelper::renderModule($module); ?>

	</div>

	<script type="text/javascript">

		js = jQuery.noConflict();
		js(document).ready(function () {
			js("#btl-buttonsubmit").click(function(){
				var sid = '<?php echo $sid; ?>';
				
				js("#btl-check-user-error").hide();

				js(".btl-error-detail").hide();



				var emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;

				if(!emailRegExp.test(js("#btl-input-user-check-email").val())){		

					js("#btl-check-user-error").html(btlOpt.MESSAGES.EMAIL_INVALID).show();

					js("#btl-input-user-check-email").focus().select();

					return false;

				}

				var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&amp;view=address&amp;step=4&amp;status=notfound');?>";
				var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&amp;view=address&amp;step=4#openModalLogin');?>";
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
						window.location.href = loginurl;
					}else{
						//alert("2");
						//alert("yes");
						/*alert(loginurl);*/

						window.location.href = loginurl;
					}
				}else{
					/*alert(redirecturl);*/
					window.location.href = redirecturl;
		   			//alert("no");
		   		}
		   	},
		   	error: function (XMLHttpRequest, textStatus, errorThrown) {
		   		// alert(textStatus + ': Ajax request failed');
		   	}
		   });
				return false;
			});

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

			<div class="alert alert-success">

				<div class="alert-message">

					please Check email address exit or not					

				</div>

			</div>

			<form name="btl-formCheckUser" class="btl-formlogin" action="" method="post">

				<div id="btl-check-user-in-process"></div>	

				<h3><?php echo JText::_('COM_EVENT_MANAGE_ENTER_EMAIL_ADDRESS') ?></h3>



				<div class="spacer"></div>



				<div class="btl-error" id="btl-check-user-error"></div>

				<div class="btl-field">

					<div class="btl-label"><?php echo JText::_( 'MOD_BT_EMAIL' ); ?></div>

					<div class="btl-input">

						<input id="btl-input-user-check-email" type="text" name="email1" />

					</div>

				</div>

				<div class="btl-buttonsubmit">						

					<button id="btl-buttonsubmit" class="btl-buttonsubmit" onclick="return checkUserAjax()" >

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

				<div class="alert alert-success">

					<div class="alert-message">

						Please login first then move ahead					

					</div>

				</div>

				<?php echo JModuleHelper::renderModule($loginmodule); ?>

			</div>	

		</div>

	</div>



</div>