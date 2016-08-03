<?php 

jimport('joomla.application.module.helper');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
$module = JModuleHelper::getModule('mod_bt_login');

?>

<?php
if(isset($this->errors) && $this->errors != ''){ ?>
<div class="error-class">
	<?php echo $this->errors; ?>
</div>
<?php } ?>
<?php
if(!empty($this->sid)){
	$sid = '&sid='.$this->sid;
}


$pag1url = JRoute::_('index.php?option=com_event_manage&view=event&step=1'.$sid);
$pag3url = JRoute::_('index.php?option=com_event_manage&view=proposal&step=3'.$sid);
?>
<div class="page1 span12">
	<div class="form-title"> <h1>Referral Page</h1> </div>
	<div class="form-body left span6">			
		<form action="" method="post" enctype="multipart/form-data" id="page6b" class="form-validate">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php //echo $this->form->getLabel('created_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label">
					“Thank you for filling in the Performsure form, 
					we cannot process a Policy online for you as the following answers do not match our criteria”</div>
				</div>
				<table>
					<tbody>
						<tr>
							<th>Page</th>
							<th>Question</th>
							<th>Your Answer</th>
							<th>Button</th>
						</tr>
						<?php 
						$n = 0;
						foreach ($this->refferal as $value) { $n++; ?>

						<?php if($value == 'perfomers') { ?>
						<tr>
							<td><?php echo $n;?></td>
							<td>Number of Performers</td>
							<td>You have indicated that you have 13 or more performers</td>
							<td>
								<div class="">
									<a href="<?php echo $pag1url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
								</div>
							</td>
						</tr>
						<?php }
						if($value == 'annualincome'){ ?>
						<tr>
							<td><?php echo $n;?></td>
							<td>Estimated Annual Income</td>
							<td>You have indicated that your estimated annual income is $250,000 or more</td>
							<td>
								<div class="">
									<a href="<?php echo $pag1url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
								</div>
							</td>
						</tr>
						<?php }
						if($value == 'selfpromote'){ ?>
						<tr>
							<td><?php echo $n;?></td>
							<td>Do you intend to hire out a performance venue to self-promote or stage your own performance?</td>
							<td>You have answered YES</td>
							<td>
								<div class="">
									<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
								</div>
							</td>
						</tr>
						<?php 
					}
					if($value == 'refusedinsurance'){ ?>
					<tr>
						<td><?php echo $n;?></td>
						<td>Have you previously been refused insurance or had your insurance cancelled by an insurer or have had special conditions, increased premiums or increased excesses imposed on any policy of insurance by an insurer?</td>
						<td>You have answered YES</td>
						<td>
							<div class="">
								<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
							</div>
						</td>
					</tr>
					<?php 
					}
					if($value == 'liabilityclaim'){ ?>
					<tr>
						<td><?php echo $n;?></td>
						<td>Have you suffered any public liability claims or have caused incidents that give rise to public liability claim?</td>
						<td>You have answered YES</td>
						<td>
							<div class="">
								<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
							</div>
						</td>
					</tr>
					<?php }
					if($value == 'criminaloffence'){ ?>
					<tr>
						<td><?php echo $n;?></td>
						<td>Have you been charged or convicted of a criminal offence (excluding driving convictions) in the last 10 years?</td>
						<td>You have answered YES</td>
						<td>
							<div class="">
								<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
							</div>
						</td>
					</tr>
					<?php }
					if($value == 'dangerousactivities'){ ?>
					<tr>
						<td><?php echo $n;?></td>
						<td>Audience participation with use of fire, sporting, hazardous or dangerous activities</td>
						<td>You have answered YES</td>
						<td>
							<div class="">
								<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
							</div>
						</td>
					</tr>
					<?php }
					if($value == 'pyrotechnics'){ ?>
					<tr>
						<td><?php echo $n;?></td>
						<td>Fireworks or pyrotechnics</td>
						<td>You have answered YES</td>
						<td>
							<div class="">
								<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
							</div>
						</td>
					</tr>
					<?php }
					if($value == 'animals'){ ?>
					<tr>
						<td><?php echo $n;?></td>
						<td>Use of animals</td>
						<td>You have answered YES</td>
						<td>
							<div class="">
								<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
							</div>
						</td>
					</tr>
					<?php }
					if($value == 'amusementrides'){ ?>
					<tr>
						<td><?php echo $n;?></td>
						<td>Use of amusement rides or devices</td>
						<td>You have answered YES</td>
						<td>
							<div class="">
								<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
							</div>
						</td>
					</tr>
					<?php }
					if($value == 'workshops'){ ?>
					<tr>
						<td><?php echo $n;?></td>
						<td>Running of workshops</td>
						<td>You have answered YES</td>
						<td>
							<div class="">
								<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
							</div>
						</td>
					</tr>
					<?php }
					if($value == 'northamerica'){ ?>
					<tr>
						<td><?php echo $n;?></td>
						<td>Any activities conducted in North America</td>
						<td>You have answered YES</td>
						<td>
							<div class="">
								<a href="<?php echo $pag3url; ?>" value="Modify" class="btn btn-primary" >Modify</a>
							</div>
						</td>
					</tr>
					<?php }
					}
					?>
			</tbody>
		</table>
		<div class="control-group">
			“If your answer/s are all correct, then we require that you speak to one of our representatives who will help you complete your policy request.
			Please click on Save & Speak to an Expert button below and one of our staff will be in touch with you shortly”
		</div>
		<div class="control-group pull-left MyMargin">
			<div class="controls">
				<?php if($this->useremail) { ?>
				<input type="button" class="btn btn-primary" id="SaveComeBackRefreal" name="Savedata" value="Save & Come back Later">
				<?php } else { ?>
				<a id="create-user" href="#openModalEmail">
					<input type="button" class="btn btn-primary" id="SaveComeBack" name="Savedata" value="Save & Come back Later">
				</a>
				<?php }?>
				<!-- <a id="create-user" href="#openModalEmail">
					<input type="button" class="btn btn-primary" id="SaveComeBack" name="Savedata" value="Save & Come back Later">
				</a> -->
				<input type="button" class="btn btn-primary" id="SpeakToAnExpert" name="SpeakToAnExpert" value="Speak To An Expert">
			</div>
		</div>
		<input type="hidden" name="page6b" value="page6b">
	</form>				
	</div>
	<div>
		<?php //echo JModuleHelper::renderModule($module); ?>
	</div>

	<script type="text/javascript">

		js = jQuery.noConflict();

		js(document).ready(function () {

				js("#SaveComeBack").click(function(){
					var email = "<?php echo $this->AllPageData['data']['page1']['email']; ?>";
					js("#btl-input-user-check-email").val(email);
				});

				js("#SpeakToAnExpert").click(function(){
					var input = js("<input>").attr("type", "hidden").attr("name", "jform[SpeakToAnExpert]").val('SpeakToAnExpert');
					var submit = js("<input>").attr("type", "submit").attr("name", "submit").attr("id", "submit").attr("style", "display:none").val('submit');
					js('#page6b').append(js(input));
					js('#page6b').append(js(submit));
					/*alert("SpeakToAnExpert");*/
					js("#submit").click();
				});

				js("#SaveComeBackRefreal").click(function(){
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

					var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=referral&step=6b&status=notfound');?>";
					var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=referral&step=6b&status=found');?>";
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

					var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=referral&step=6b&status=notfound');?>";
					var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=referral&step=6b&status=found');?>";
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
									/*alert("yes");*/
									window.location.href = loginurl+'&amp;email1='+email;
								}
							}else{

								window.location.href = redirecturl+'&amp;email1='+email;
								/*alert("no");*/
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
					var submit = js("<input>").attr("type", "submit").attr("name", "submit").attr("id", "submit").val('submit');
					js('#page6b').append(js(input));
					js('#page6b').append(js(submit));
					js("#submit").click();
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
					<button id="btl-buttonsubmit" class="btn btn-primary">
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