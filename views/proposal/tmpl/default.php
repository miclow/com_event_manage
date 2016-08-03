<?php 



jimport('joomla.application.module.helper');

JHTML::_('behavior.formvalidation');

JHTML::_('behavior.tooltip');

$module = JModuleHelper::getModule('mod_bt_login');





/*$premium = $this->premium;*/

//echo '<pre>'; print_r($event); print_r($term); echo '</pre>';

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
				if($key != 'proposal'){
					$active = 'completed';
					$color = '#65d074';		
				}
			}else{
				$active = '';		
			}

			if($key == 'proposal'){
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

	<div class="form-title"> <h3>Page 3: Proposal</h3> </div>

	<div class="form-body">			

		<form action="" method="post" enctype="multipart/form-data" id="page3" name="page3" class="form-validate">

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>

			</div>

			<div class="control-group">

				<div class="control-label"><?php //echo $this->form->getLabel('created_by'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>

			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('selfpromote'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('selfpromote'); ?></div>

				<div id="selfpromote-msg" style="padding:10px; display:none;">
					<div style="color:red; font-weight:600; font-style:italic; padding:20px 0px;">“Please note this policy does not provide cover for hiring of a venue or self-promoting / staging of your own performance. If cover is required for these activities please complete our Entertainment & Events Liability** proposal or contact our office on 1300 655 424.”</div>

					<span>
					<a href="https://www.entertainmentinsurance.net.au/entertainment-events-liability-insurance" target="_blank">https://www.entertainmentinsurance.net.au/entertainment-events-liability-insurance</a></span>
				</div>
			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('harmlessorindemnity'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('harmlessorindemnity'); ?></div>

				<div id="harmlessorindemnity-msg" style="padding:10px; display:none;">
					<div style="color:red; font-weight:600; font-style:italic; padding:20px 0px;">“A hold harmless or indemnity agreement is whereby one party assumes the liability risks of another party under contract. Your public liability policy is to cover your legal liability and no one else’s, therefore claims arising from these agreements is excluded. Please do not sign these agreements. If you do without first referring them to your insurer, you may not be covered in the event of a claim.”</div>
				</div>
			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('subcontractors'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('subcontractors'); ?></div>

				<div id="subcontractors-msg" style="padding:10px; display:none;">
					<div style="color:red; font-weight:600; font-style:italic; padding:20px 0px;">“Please note that this policy will only provide cover for contracted performers, if you engage contractors/subcontractors to perform any other activities cover will not be provided. Please ensure that carry their own Public Liability Insurance”</div>
				</div>
			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('refusedinsurance'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('refusedinsurance'); ?></div>

			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('liabilityclaim'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('liabilityclaim'); ?></div>

			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('criminaloffence'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('criminaloffence'); ?></div>

			</div>

			<div class="control-group">

				Will you performance activities include:

			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('dangerousactivities'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('dangerousactivities'); ?></div>

			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('pyrotechnics'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('pyrotechnics'); ?></div>

			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('animals'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('animals'); ?></div>

			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('amusementrides'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('amusementrides'); ?></div>

			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('workshops'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('workshops'); ?></div>

			</div>

			<div class="control-group">

				<div class="control-label"><?php echo $this->form->getLabel('northamerica'); ?></div>

				<div class="controls"><?php echo $this->form->getInput('northamerica'); ?></div>

			</div>

			<div class="control-group pull-left MyMargin">

				<div class="control-label"></div>

				<div class="controls ">
					<?php 
					if($this->sid) {
						$sid = '&sid='.$this->sid;
					}
					?>
					<a href="index.php?option=com_event_manage&view=quotation&step=2<?php echo $sid; ?>" value="Back" class="btn btn-primary" >BACK</a>

				</div>

			</div>
			<div class="control-group pull-left MyMargin">

				<div class="control-label"></div>

				<div class="controls"><?php echo $this->form->getInput('next'); ?></div>

				<input type="hidden" name="page3" value="page3">

			</div>	

			<div class="control-group pull-left MyMargin">

				<div class="controls">
					<?php if($this->useremail) { ?>
					<input type="button" class="btn btn-primary" id="SaveComeBackLeter" name="Savedata" value="Save & Exit">
					<?php } else { ?>
					<a id="create-user" href="#openModalEmail">
						<input type="button" class="btn btn-primary" id="SaveComeBack" name="Savedata" value="Save & Exit">
					</a>
					<?php } ?>


				</div>

			</div>
		</form>				

	</div>

		<?php //echo JModuleHelper::renderModule($module); ?>

</div>

	

<script type="text/javascript">
		js = jQuery.noConflict();
		js(document).ready(function () {

			checked();

			function checked(){
				if (js('#jform_selfpromote0').is(':checked')) {
			        js('#selfpromote-msg').show();
			    } else {
			        js('#selfpromote-msg').hide();
			    }

			    if (js('#jform_harmlessorindemnity0').is(':checked')) {
			        js('#harmlessorindemnity-msg').show();
			    } else {
			        js('#harmlessorindemnity-msg').hide();
			    }

			    if (js('#jform_subcontractors0').is(':checked')) {
			        js('#subcontractors-msg').show();
			    } else {
			        js('#subcontractors-msg').hide();
			    }
			}

			/* selfpromote */
			js('#jform_selfpromote').change(function() {
				if (js('#jform_selfpromote0').attr('checked')) {
			        js('#selfpromote-msg').show();
			    } else {
			        js('#selfpromote-msg').hide();
			    }
		    });
			/* End */

			/* harmlessorindemnity */
			js('#jform_harmlessorindemnity').change(function() {
				if (js('#jform_harmlessorindemnity0').attr('checked')) {
			        js('#harmlessorindemnity-msg').show();
			    } else {
			        js('#harmlessorindemnity-msg').hide();
			    }
		    });
			/* End */

			/* subcontractors */
			js('#jform_subcontractors').change(function() {
				if (js('#jform_subcontractors0').attr('checked')) {
			        js('#subcontractors-msg').show();
			    } else {
			        js('#subcontractors-msg').hide();
			    }
		    });
			/* End */

			js("#Savedata").click(function(){
				var email = "<?php echo $this->frontdata->data['page1']['email']; ?>";
				js("#btl-input-user-check-email").val(email);
			});

			js("#SaveComeBackLeter").click(function(){

					// alert("SaveComeBackLeter");

					js("#btl-check-user-error").hide();

					js(".btl-error-detail").hide();

					var email = js("#btl-input-user-check-email").val();
					
					var sid = '<?php echo $sid; ?>';

					var emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;

					if(!emailRegExp.test(js("#btl-input-user-check-email").val())){		

						js("#btl-check-user-error").html(btlOpt.MESSAGES.EMAIL_INVALID).show();

						js("#btl-input-user-check-email").focus().select();

						return false;

					}
					// alert(sid);
					var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=proposal&step=3&status=notfound');?>";
					var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=proposal&step=3&status=found');?>";
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
									// alert("yes");
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
							// alert(textStatus + ': Ajax request failed');
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

				var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=proposal&step=3&status=notfound');?>";

				var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=proposal&step=3&status=found');?>";

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





								window.location.href = loginurl+'&amp;email1'+email;

							}else{





								window.location.href = loginurl+'&amp;email1'+email;

							}



						}else{

							window.location.href = redirecturl+'&amp;email1'+email;
						}





					},

					error: function (XMLHttpRequest, textStatus, errorThrown) {

									// alert(textStatus + ': Ajax request failed');

								}

							});

				return false;
			});



			var a = "<?php echo $this->newuserid; ?>";

			if(a){


				/*alert('Yes return sucess');*/

				var input = js("<input>").attr("type", "hidden").attr("name", "jform[userid]").val(a);

				js('#page3').append(js(input));

				/*js("#page3").submit();*/

				js("#submit").click();

			}else{

							/*alert(a);
							alert('no');*/

				}
		});
</script>



<style type="text/css">

	fieldset.radio label {
    	display: inline;
    	margin-left: 5px;
    	margin-right: 5px;
	}
	
	fieldset.radio input {
	    float: inherit !important;
	    margin-left:10px !important;
	}

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
