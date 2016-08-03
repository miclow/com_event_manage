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
		<?php 
		JFactory::getApplication()->enqueueMessage($this->errors,'error');
		?>
	</div>

<?php } ?> 
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		var a = {};
		js("#jform_termconditions").change(function(){
	    	if(js(this).is(":checked")){
	    		a['c1'] = 'Y';
		    }else{       
		    	a['c1'] = 'N';
		    }
		    checked(a);
		})
		js("#jform_fsgread").change(function(){
	    	if(js(this).is(":checked")){
	    		a['c2'] = 'Y';
		    }else{       
		    	a['c2'] = 'N';                    
		    }
		    checked(a);
		})
		function checked(a){
			if(a['c1'] == 'Y' && a['c2'] == 'Y'){
				var r = '<?php echo $this->form->getInput("next"); ?>';
				js('#next').append(r);
			}else if(js('#jform_termconditions').is(":checked") && js('#jform_fsgread').is(":checked") ){
				var r = '<?php echo $this->form->getInput("next"); ?>';
				js('#next').append(r);
			}else{
				js('#submit').remove();
			}
		}
		checked(a);
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
					if($key != 'declaration'){
						$active = 'completed';
						$color = '#65d074';			
					}
				}else{
						$active = '';		
				}
				if($key == 'declaration'){
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
	<div class="form-title"> <h3>Page 5: Declaration</h3> </div>
	  <div class="form-body left span8">			
		<form action="" method="post" enctype="multipart/form-data" id="page5" class="form-validate">
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
					When you order you accept our <a href="http://182.160.156.75/~manlyweb/index.php?option=com_content&view=article&id=8" target="_blank">Terms and Conditions</a> 
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					Before accepting please read the Arena Duty of Disclosure and Declaration below
				</div>
			</div>
			<div class="control-group">
				<?php $pageURL = JURI::base(); ?>
				<div class="control-label">
					When you obtain a quotation from this website, you acknowledge that the following have been made available to you: 
					<a href="http://182.160.156.75/~manlyweb/index.php?option=com_content&view=article&id=28" target="_blank">Binder Advice Warning</a>, <a href="<?php echo $pageURL ?>/documents/PERFORMSURE WORDING - BIA GL G2 Arena Ent 2015.pdf" target="_blank">Policy Wording</a>, <a href="<?php echo $pageURL ?>/documents/Financial Services Guide.pdf" target="_blank">Financial Services Guide</a>, <a href="http://182.160.156.75/~manlyweb/index.php?option=com_content&view=article&id=29" target="_blank">Duty of Disclosure</a>, <a href="http://182.160.156.75/~manlyweb/index.php?option=com_content&view=article&id=5" target="_blank">Privacy Statement</a> & <a href="http://182.160.156.75/~manlyweb/index.php?option=com_content&view=article&id=32" target="_blank">General Advice Warning</a>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('termconditions'); ?></div>
				<div class="controls">
					<?php //echo $this->form->getInput('termconditions'); ?>
					<input type="checkbox" aria-required="true" required="required" class="required invalid" value="1" id="jform_termconditions" name="jform[termconditions]" aria-invalid="true">
				</div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('fsgread'); ?></div>
				<div class="controls">
					<?php //echo $this->form->getInput('fsgread'); ?>
					<input type="checkbox" aria-required="true" required="required" class="required" value="1" id="jform_fsgread" name="jform[fsgread]">
				</div>
			</div>
            
            <div class="control-group pull-left MyMargin">
				<div class="control-label "></div>
				<div class="controls ">
					<?php 
						if($this->sid) {
							$sid = '&sid='.$this->sid;
						}
					?>
					<a href="index.php?option=com_event_manage&view=address&step=4<?php echo $sid; ?>" value="Back" class="btn btn-primary" >BACK</a>
				</div>
			</div>
			<div class="control-group pull-left MyMargin">
				<div class="control-label"></div>
				<div id="next" class="controls"></div>
                <input type="hidden" name="page5" value="page5">
			</div>	
    <input type="hidden" name="jform[sid]" id="jform_sid" value="<?php echo $this->sid; ?>">
    <div class="control-group pull-left MyMargin">
			<div class="controls">
				<!-- <a id="create-user" href="#openModalEmail"> -->
					<input type="button" class="btn btn-primary" id="SaveComeBack" name="Savedata" value="Save & Exit">
				<!-- </a> -->
				
				<?php //echo JModuleHelper::renderModule($module); ?>
			</div>
		</div>
	
	</form>				
	</div>
	
	
	<div>
		<?php //echo JModuleHelper::renderModule($module); ?>
	</div>
	<script type="text/javascript">
	js = jQuery.noConflict();

	js(document).ready(function () {

	  js("#SaveComeBack").click(function(){

		var email1 = js("#jform_confirmemail").val();

    		if(email1 != "" ){

    			
    			var sid = '<?php echo $sid; ?>';
    			
    			if(js('#jform_termconditions').prop("checked") != true){
	                js( "#jform_termconditions" ).prop( "checked", true );
	                
	            }
	            if(js('#jform_fsgread').prop("checked") != true){
	                js( "#jform_fsgread" ).prop( "checked", true );
	            }

	            var r = '<?php echo $this->form->getInput("next"); ?>';
				js('#next').append(r);

	    		var emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;

			/*	if(!emailRegExp.test(email2)){		

					js("#jform_confirmemail").html(btlOpt.MESSAGES.EMAIL_INVALID).show();

					js("#jform_confirmemail").focus().select();

					return false;

				}*/

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

								
								/*window.location.href = loginurl;*/

								var input = js("<input>").attr("type", "hidden").attr("name", "jform[status]").val('found');

								js('#page5').append(js(input));

								js("#submit").click();

							}else{

								 

								/*window.location.href = loginurl;*/

							var input = js("<input>").attr("type", "hidden").attr("name", "jform[status]").val('found');

								js('#page5').append(js(input));

								js("#submit").click();

							}

				   		}else{


				   			var input = js("<input>").attr("type", "hidden").attr("name", "jform[status]").val('notfound');

							js('#page5').append(js(input));

							js("#submit").click();

				   			//window.location.href = redirecturl;	

				   		}



				   },

				   error: function (XMLHttpRequest, textStatus, errorThrown) {

						// alert(textStatus + ': Ajax request failed');

				   }

				});

				return false;

    		}

		});

	js("#btl-buttonsubmit").click(function(){

		js("#btl-check-user-error").hide();
		js(".btl-error-detail").hide();

		var emailRegExp = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/;
		if(!emailRegExp.test(js("#btl-input-user-check-email").val())){		
			js("#btl-check-user-error").html(btlOpt.MESSAGES.EMAIL_INVALID).show();
			js("#btl-input-user-check-email").focus().select();
			return false;
		}
		var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=declaration&step=5&status=notfound');?>";
		var loginurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=declaration&step=5&status=found');?>";
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
							window.location.href = loginurl;
						}
						
			   		}else{
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
		 
				var a = "<?php echo $this->newuserid; ?>";
				
				if(a){
					/*alert('Yes return sucess');*/
					var input = js("<input>").attr("type", "hidden").attr("name", "jform[userid]").val(a);
					js('#page5').append(js(input));
					js("#page5").submit();
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