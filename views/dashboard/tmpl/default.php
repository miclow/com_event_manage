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

<div class="page1">
	<div class="form-title"> <h3>Dashboard Page </h3></div>
	<div class="row-fluid top15">
		<div class="span12" style="margin-left:0">
			<div class="form-body  span12">			
				<div class="row-fluid top15">
					<?php 
						$url = JRoute::_('index.php?option=com_event_manage&view=event&step=new'); 
					?>
					<!-- <a href="<?php echo $url;?>">Add New Policy</a> -->
					<a href="<?php echo $url;?>" value="Add New Policy" class="btn btn-primary" >Add New Policy</a>
					<table cellspacing="10" cellpadding="10">
						<tbody>
							<tr>
								<th>Order No.</th>
								<th>Policy Number</th>
								<th>Insurance Name</th>
								<th>Premium</th>
								<th>Start Date</th>
								<th>Expired Date</th>
								<th>Status</th>
								<th>Action</th>
								<th>Documents</th>
							</tr>
							<?php 
								$i = 0;
								$curuncysymbol = '$';
								foreach ($this->userdetail as $value) {
									# code...
									$data = unserialize($value->data);
									
									$expirydate = date('d-m-Y h:i:s A',strtotime('+1 Year 16 hours',strtotime($data['page1']['start_date'])));
									$today = date('d-m-Y h:i:s A');

									if(!empty($data['page6']['totalpremium'])){
										$totalpremium = $data['page6']['totalpremium'];
									}else if(!empty($data['page2']['totalpremium'])){
										$totalpremium = $data['page2']['totalpremium'];
									}else{
										$totalpremium = '--';
									}
									
							?>
							<tr>
								<td><?php echo $value->id; ?></td>
								<td><?php if(!empty($value->policy_number)) { echo $value->policy_number;} else { echo "--"; }?></td>
								<td><?php echo $data['page1']['insurencename']; ?></td>
								<td><?php if(!empty($totalpremium) && $totalpremium >=1 ){ echo $curuncysymbol.number_format($totalpremium,2); }else{ echo '--'; } ?></td>
								<td><?php echo date("d-m-Y",strtotime($data['page1']['start_date'])); ?></td>
								<td><?php echo date("d-m-Y",strtotime('+1 year',strtotime($data['page1']['start_date']))); ?></td>
								<td><?php if(strtotime($today) >= strtotime($expirydate)){
												echo $value->activity_status; 
											}else{
												echo $value->activity_status; 
											}
									?>
								</td>
								<td>
									<?php 
										foreach ($this->pageArr as $key => $pvalue){
											if($pvalue == $value->onpage){		
												$step = $pvalue; 									
												if($value->activity_status != 'Active'){
													$statusurl = JRoute::_('index.php?option=com_event_manage&view='.$key.'&step='.$step.'&sid='.$value->id);
												}else{
													$statusurl = "javascript:void(0);";
												}

												if(strtotime($today) >= strtotime($expirydate) && $value->activity_status == 'Expire'){
													$step = 'renew';
													$expireurl = JRoute::_('index.php?option=com_event_manage&view=event&step='.$step.'&sid='.$value->id);
												}
											}
										}
										
										
										$view = '<div><a href="index.php?option=com_event_manage&view=usersdetail&show=user&id='.$value->id.'">View</a></div>';
										if($value->activity_status == 'Referral'){
											echo $view;
										}
										if($value->activity_status == 'Hold'){
											echo $view;
										}
										if($value->activity_status == 'Expire'){
											if(strtotime($today) >= strtotime($expirydate)){
												echo '<div><a href="'.$expireurl.'">Renew</a></div>';
											}
										}
										if($value->activity_status == 'Active'){
											// echo '<div><a href="javascript:void(0);">modify</a></div>';
											
												echo $view;
												$cancelurl = JRoute::_('index.php?option=com_event_manage&view=dashboard&status=cancel&oid='.$value->id);
												echo '<div><a href="'.$cancelurl.'">cancel</a></div>';
												// if(!empty($value->policy_filename)){
												// 	echo '<div><a href="index.php?option=com_event_manage&view=dashboard&file='.$value->policy_filename.'">Download PDF</a></div>';
												// }
											
										}

										if($value->activity_status == 'Unfinished' || $value->activity_status == 'Unpaid'){
											echo $view;
											echo '<div><a href="'.$statusurl.'">edit</a></div>';
											if($value->activity_status == 'Unpaid'){
												echo '<div><a href="'.$statusurl.'">pay</a></div>';	
											}else{
												echo '<div><a href="'.$statusurl.'">pay</a></div>';
												//echo '<div><a href="javascript:void(0);">pay</a></div>';
											}
											if($value->status == 1){
												$status = 'N';
											}else if($value->status == 0){
												$status = 'Y';
											}
											$deleteurl = JRoute::_('index.php?option=com_event_manage&view=dashboard&status='.$status.'&oid='.$value->id);
											echo '<div><a href="'.$deleteurl.'">delete</a></div>';
										}
									?>
									<!-- <a href="<?php echo $statusurl;?>">
										<?php echo $value->activity_status; ?>
									</a> -->
								</td>
								<td>
									<ul style="margin: 0; padding-left: 10px;">
										<?php 
											$policy_wording = 'PERFORMSURE_POLICY_WORDING_BIA_GL_G2_Arena_Ent_1_2016.pdf';
											if($value->activity_status == 'Active'){
												// echo '<div><a href="javascript:void(0);">modify</a></div>';
												
												if(!empty($value->policy_filename)){
													echo '<li><a href="index.php?option=com_event_manage&view=dashboard&file='.$value->policy_filename.'">Invoice-Performsure Policy</a></li>';
												}
												if(!empty($value->certificate_of_currency)){
													echo '<li><a href="index.php?option=com_event_manage&view=dashboard&file='.$value->certificate_of_currency.'">Certificate of Currency</a></li>';
												}
												if(!empty($policy_wording)){
													echo '<li><a href="index.php?option=com_event_manage&view=dashboard&file='.$policy_wording.'">Performsure Policy Wording 2016</a></li>';
												}
												if(!empty($value->performsure_questionnaire_response)){
													echo '<li><a href="index.php?option=com_event_manage&view=dashboard&file='.$value->performsure_questionnaire_response.'">Performsure Questionnaire Response Summery</a></li>';
												}
												
											}else{
												echo '--';
											}
										?>
									</ul>
									<!-- <a href="<?php echo $statusurl;?>">
										<?php echo $value->activity_status; ?>
									</a> -->
								</td>
							</tr>
							<?php
								$i++;
								}
							?>
						</tbody>
					</table>
					<input type="hidden" name="dashboard" value="dashboard">		
				</div>
			</div>
		</div>
	</div>
</div>