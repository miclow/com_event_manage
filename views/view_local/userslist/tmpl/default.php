<?php 

jimport('joomla.application.module.helper');

JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

if(isset($this->errors) && $this->errors != ''){ ?>
<div class="error-class">
	<?php echo $this->errors; ?>
</div>
<?php } ?> 
<script type="text/javascript">
	js = jQuery.noConflict();
	
	function ajax_post(a,activity){
		// alert(a);
		//alert(activity);
	    // Create our XMLHttpRequest object
	    var hr = new XMLHttpRequest();
	    // Create some variables we need to send to our PHP file
	    var url = "index.php?option=com_event_manage&task=changeUnpaidToPaid";
	    var vars = "pid="+a;
	    var redirecturl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=userslist&pid=');?>";
	    // var referralurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=userslist#openModalEmail');?>";
	    var referralurl = "<?php echo JRoute::_('index.php?option=com_event_manage&view=userslist');?>";
	    hr.open("POST", url, true);
	    // Set content type header information for sending url encoded variables in the request
	    hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    // Access the onreadystatechange event for the XMLHttpRequest object
	    hr.onreadystatechange = function() {
	    	if(hr.readyState == 4 && hr.status == 200) {
	    		var return_data = hr.responseText;
	    		obj = JSON.parse(return_data);	
	    		// alert(obj.status);
	    		if(obj.status == 'Unfinished'){
	    			var msg = 'You completed '+obj.onpage+' Steps And your status '+obj.status;
	    			if(confirm(msg)){
	    				// alert("Yes");
	    				window.location.href = redirecturl+a;
	    			}else{
	    				// alert("No");
	    			}
	    		}else if(obj.status == 'Referral'){
	    			//document.getElementById("activity").value = activity;
	    			var input = document.createElement("input");
		                input.type = "hidden";
		                input.name = "jform[orderid]";
		                if(obj.referral == '13+'){
		                	input.id = "prem_order_id";
		                }else{
		                	input.id = "order_id";	
		                }
		                
		                input.value = a;
		            var onpageinput = document.createElement("input");
		                onpageinput.type = "hidden";
		                onpageinput.name = "jform[onpage]";
		                
		                if(obj.referral == '13+'){
		                	onpageinput.id = "prem_onpage";
		                }else{
		                	onpageinput.id = "onpage";
		                }
		                onpageinput.value = obj.onpage;
		                // alert(obj.referral);
		                if(obj.onpage == 1 && obj.referral == '13+'){
		                	var modal = '#openModalReffralOne';
		                	var container = document.getElementById("prem-btl-buttonsubmit");
			                var orderid = document.getElementById("prem_order_id");
			                if(document.getElementById("prem_order_id")){
			                	container.removeChild(orderid);
			                	container.removeChild(document.getElementById("prem_onpage"));
			                }
			                container.appendChild(input);
			                container.appendChild(onpageinput);
		                }else{
		                	var modal = '#openModalReffralThird';
		                	var container = document.getElementById("btl-buttonsubmit");
			                var orderid = document.getElementById("order_id");
			                if(document.getElementById("order_id")){
			                	container.removeChild(orderid);
			                	container.removeChild(onpage);
			                }
			                container.appendChild(input);
			                container.appendChild(onpageinput);
		                }
	    				window.location.href = referralurl+modal;
	    		}else if(obj.status == 'Unpaid'){
	    			window.location.href = redirecturl+a;
	    		}
	    	}
	    }
	    // Send the data to PHP now... and wait for response to update the status div
	    hr.send(vars); // Actually execute the request
	}

</script>

<div class="page1">
	<div class="form-title"> <h1>Users List Page </h1></div>
	<div class="row-fluid top15">
		<div class="span12" style="margin-left:0;">
			<div class="form-body span12">			
				<div class="row-fluid top15">
				<!-- <a href="index.php?option=com_event_manage&view=userslist&file=test.pdf">download</a> -->
					<form name="searche" method="post" action="">
						<input type="text" name="s_ordernumber" placeholder="Order Number" value="<?php echo $this->s_ordernumber; ?>">
						<input type="text" name="s_policynumber" placeholder="Policy Number" value="<?php echo $this->s_policynumber; ?>">
						<input type="text" name="s_firstname" placeholder="First Name" value="<?php echo $this->s_firstname; ?>">
						<input type="text" name="s_lastname" placeholder="Last Name" value="<?php echo $this->s_lastname; ?>">
						<input type="text" name="s_email" placeholder="Email" value="<?php echo $this->s_email; ?>">
						<input type="submit" name="search" class="btn btn-primary" value="search">
					</form>
					<table cellpadding="10" cellspacing="10">
						<tbody>
							<tr>
								<th>Order No</th>
								<th>Policy Number</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Email</th>
								<th>Insurance Name</th>
								<th>Premium</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
							<?php 
							$activitystatus = array('Unfinished','Unpaid','Referral','Hold');
							$i=0;
							
							if(!empty($this->userdetail)){
								foreach ($this->userdetail as $value) {

									$data = unserialize($value->data);
									
									if($value->onpage == '6'){
										$totalpremium = $data['page6']['totalpremium'];
									}else if($value->onpage == '2'){
										$totalpremium = $data['page2']['totalpremium'];
									}else{
										
											$premium = $data['page2']['totalpremium'];
											$data1 = JFactory::getDBO();        
							                $query = "SELECT * FROM #__event_manage_term WHERE activity = 'Excharge' AND order_id =".$value->id ;
							                $query .= " AND name IN ('premium','brokerfee')";
							                $data1->setQuery($query);
							                $extraprimum = $data1->loadObjectList();
											$totalpremium1 = round($extraprimum[0]->price,2);
											if(!empty($premium)){
												$totalpremium = $premium;	
											}else if(!empty($totalpremium1)){
												$totalpremium = $totalpremium1;	
											}else{
												$totalpremium = '--';	
											}
									}

									?>
									<tr>
										<td><?php echo $value->id;?></td>
										<td><?php if(!empty($value->policy_number)) { echo $value->policy_number;} else { echo "--"; }?></td>
										<td><?php if(!empty($data['page4']['firstname'])){ echo $data['page4']['firstname'];}else{ echo "--";}?></td>
										<td><?php if(!empty($data['page4']['lastname'])){ echo $data['page4']['lastname'];}else{ echo "--";}?></td>
										<td>
											<?php 
											if(!empty($data['page4']['email'])){
												echo trim($data['page4']['email']);
											}else if(!empty($data['page1']['email'])){
												echo trim($data['page1']['email']);
											}else{
												echo "--";
											}	
											?>
										</td>
										<td><?php if(!empty($data['page1']['insurencename'])){ echo $data['page1']['insurencename'];}else{ echo "--";} ?></td>
										<td><?php if(!empty($totalpremium)){ echo $totalpremium;}else{ echo "--";} ?></td>
										<td><?php if(!empty($value->activity_status)){ echo $value->activity_status;}else{ echo "--";} ?></td>
										<td>
											<?php
											echo '<div><a href="index.php?option=com_event_manage&view=usersdetail&show=staff&id='.$value->id.'">View</a></div>';

											if(in_array($value->activity_status, $activitystatus)){
												if($value->activity_status == 'Unpaid'){
													echo '<div><a href="index.php?option=com_event_manage&view=userslist&pid='.$value->id.'">Change To Paid</a></div>';
												}
												// else{
												// 	echo '<div><a href="javascript:void(0);">Change To Paid</a></div>';
												// }
											}
											if($value->activity_status == 'Referral' || $value->activity_status == 'Hold'){
												echo '<div>';
												//echo '<a href="index.php?option=com_event_manage&view=usersdetail&id='.$value->id.'">add Extras</a>';
												echo '<a onclick="ajax_post('.$value->id.');" href="javascript:void(0);">add Extras</a>';
												echo '</div>';
											}

											?>
										</td>
									</tr>
									<?php
									$i++;
								} // End for each...
							} else { // End If...
								echo "<tr><td colspan='8'>There is no records.</td></tr>";
							}
							?>
						</tbody>
					</table>
					<input type="hidden" name="userlist" value="userlist">		
				</div>
				<?php echo $this->pagination; ?>
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	.modalDialog {
		position: fixed;
		top: 0;
		right: 0;
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

<div id="openModalReffralThird" class="modalDialog">
	<div class="inner-div">
		<a href="#close" title="Close" class="close">X</a>
		<div id="btl-check-user" class="btl-content-block">
			<form name="add-extra-charge" id="extrafieldform" class="btl-extrafieldform" action="#" method="post">
				<div id="btl-check-user-in-process"></div>	
				<h3>Add Extra Field</h3>
				<div class="spacer"></div>
				<div class="btl-error" id="btl-check-user-error"></div>
				<div class="btl-field">
					<div class="btl-input">
						Field Name: <input id="btl-input-user-check-name" required="true" type="text" name="jform[name]" />
						Value/Price: <input id="btl-input-user-check-price" required="true" type="text" name="jform[price]" />
						Description: <textarea name="jform[description]" cols="40"></textarea>
					</div>
				</div>
				<div class="btl-buttonsubmit" id="btl-buttonsubmit">						
					<!-- <button id="btl-buttonsubmit" class="btn btn-primary" onclick="return checkUserAjax()" >
						<?php //echo JText::_('CHECK_USER');?>							
					</button> -->
					<input type="hidden" name="jform[Excharge]" value="Excharge" />
					<!-- <input type="hidden" name="jform[activity]" id="activity" value="" /> -->
					<?php if($this->orderno) { ?>  					
						<input type="hidden" name="jform[orderid]" id="orderid" value="<?php echo $this->orderno; ?>" />
						<input type="hidden" name="jform[onpage]" id="onpage" value="<?php echo $this->onpage; ?>" />
					<?php } ?>
					<input type="submit" name="jform[save]" class="btn btn-primary" value="save" />
					<input type="submit" name="jform[savenew]" class="btn btn-primary" value="save & new" />
					<?php echo JHtml::_('form.token');?>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="openModalReffralOne" class="modalDialog">
	<div class="inner-div">
		<a href="#close" title="Close" class="close">X</a>
		<div id="btl-check-user" class="btl-content-block">
			<form name="add-extra-charge" id="extrafieldform" class="btl-extrafieldform" action="#" method="post">
				<div id="btl-check-user-in-process"></div>	
				<h3>Add Premium</h3>
				<div class="spacer"></div>
				<div class="btl-error" id="btl-check-user-error"></div>
				<div class="btl-field">
					<div class="control-group">
						<div class="control-label">Premium: </div>
						<div class="controls">
							<input id="btl-input-user-check-name" required="true" type="text" name="jform[premium]" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">BrokerFee: </div>
						<div class="controls">
							<input id="btl-input-user-check-price" required="true" type="text" name="jform[brokerfee]" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">Description: </div>
						<div class="controls">
							<textarea name="jform[description]" cols="40"></textarea>
						</div>
					</div>
				</div>
				<div class="btl-buttonsubmit" id="prem-btl-buttonsubmit">						
					
					<input type="hidden" name="jform[performers]" value="13+" />
					<!-- <input type="hidden" name="jform[activity]" id="activity" value="" /> -->
					<?php if($this->orderno) { ?>  					
						<input type="hidden" name="jform[orderid]" id="prem-orderid" value="<?php echo $this->orderno; ?>" />
						<input type="hidden" name="jform[onpage]" id="prem-onpage" value="<?php echo $this->onpage; ?>" />
					<?php } ?>
					<input type="submit" name="jform[save]" class="btn btn-primary" value="save" />
					<?php echo JHtml::_('form.token');?>
				</div>
			</form>
		</div>
	</div>
</div>