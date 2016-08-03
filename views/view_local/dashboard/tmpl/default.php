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
	<div class="form-title"> <h1>Dashboard Page </h1></div>
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
							</tr>
							<?php 
								$i = 0;
								foreach ($this->userdetail as $value) {
									# code...
									$data = unserialize($value->data);
									
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
								<td><?php echo $totalpremium; ?></td>
								<td><?php echo date("d-m-Y",strtotime($data['page1']['start_date'])); ?></td>
								<td><?php echo date("d-m-Y",strtotime('+1 year',strtotime($data['page1']['start_date']))); ?></td>
								<td><?php echo $value->activity_status; ?></td>
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
											}
										}
										
										echo '<div><a href="index.php?option=com_event_manage&view=usersdetail&show=user&id='.$value->id.'">View</a></div>';

										if($value->activity_status == 'Active'){
											// echo '<div><a href="javascript:void(0);">modify</a></div>';
											//<a href="index.php?option=com_event_manage&view=userslist&file=test.pdf">download</a>
											//$cancelurl = JRoute::_('index.php?option=com_event_manage&view=dashboard&status=cancel&oid='.$value->id);
											$cancelurl = JRoute::_('index.php?option=com_event_manage&view=dashboard&status=cancel&oid='.$value->id);
											echo '<div><a href="'.$cancelurl.'">cancel</a></div>';
											if(!empty($value->policy_filename)){
												echo '<div><a href="index.php?option=com_event_manage&view=dashboard&file='.$value->policy_filename.'">Download PDF</a></div>';
											}
										}

										if($value->activity_status == 'Unfinished' || $value->activity_status == 'Unpaid'){
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