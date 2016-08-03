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

<?php 
$data = unserialize($this->userdetail->data);
$currencysymbol = '$';
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
	   js('.tab-panel .tab-link a').on('click', function (e) {
	        var currentAttrValue = jQuery(this).attr('href');

	        // Show/Hide Tabs
	        //Fade effect
	        //   $('.tab-panel ' + currentAttrValue).fadeIn(1000).siblings().hide();
	        //Sliding effect
	        js('.tab-panel ' + currentAttrValue).slideDown(400).siblings().slideUp(400);

	        //Sliding up-down effect
	       // $('.tab-panel ' + currentAttrValue).siblings().slideUp(400);
	        // $('.tab-panel ' + currentAttrValue).delay(400).slideDown(400);

	        // Change/remove current tab to active
	        js(this).parent('li').addClass('active').siblings().removeClass('active');

	        e.preventDefault();
	    });
	});
</script>
<style type="text/css">
	/*----------tap panel style------*/
	.first_row{
		margin-left:2.12766% !important;
	}
 .tab-panel {
            width: 100%;
            display: inline-block;
        }

        .tab-link:after {
            display: block;
            padding-top: 20px;
            content: '';
        }

        .tab-link li {
            margin: 0px 5px;
            float: left;
            list-style: none;
        }

        .tab-link a {
            padding: 8px 15px;
            display: inline-block;
            border-radius: 10px 3px 0px 0px;
            background: #8dc8f5;
            font-weight: bold;
            color: #4c4c4c;
        }

            .tab-link a:hover {
                background: #b7c7e5;
                text-decoration: none;
            }

        li.active a, li.active a:hover {
            background: #ccc;
            color: #0094ff;
            text-decoration:none;
        }

        .content-area {
            padding: 15px;
            border-radius: 3px;
            box-shadow: -1px 1px 1px rgba(0,0,0,0.15);
            background: #fff;
            border: 1px solid #ccc;
        }

        .inactive{
            display: none;
        }

        .active {
            display: block;
        }
</style>
<div class="page1">
	<div class="form-title"> <h3>User Details Page </h3></div>
	<div class="control-group pull-left MyMargin">
		<div class="controls">
			<?php 
				if($this->show == 'user'){
					$url = 	'index.php?option=com_event_manage&view=dashboard';
				}
				if($this->show == 'staff'){
					$url = 	'index.php?option=com_event_manage&view=userslist';
				} 
			?>
			<a href="<?php echo $url; ?>" value="Back" class="btn btn-primary" >BACK</a>
		</div>
	</div>
	<div class="tab-panel">
	    <ul class="tab-link">
	        <?php 
	        	foreach ($this->pageArr as $key => $value) {
	        		
	        		$page = 'page'.$value;
	        		$link = '';

	        		if(strtoupper($key) == 'EVENT') { 
	                 	$class = 'class="active"';
	                 	$tabname = 'Requirement';
	             	}else{
	             	 	$class = '';
	             	 	$tabname = ucfirst($key);
	             	}

	        		if(array_key_exists($page, $data)){
						if(strtoupper($key) == 'EVENT') { 
							$link = '#Requirement' ;
						}else{
							$link = '#'.ucfirst($key) ;		
						}
						echo '<li '.$class.'><a href="'.$link.'">'.$tabname.'</a></li>';
					}
					 
	        	}
	        ?>
	    </ul>
	    <div class="content-area span9">
	        <div id="Requirement" class="active">
	            <div class="control-group span12 first_row">
					<div class="control-label span3">Name of Insured :</div>
					<div class="controls"><?php echo $data['page1']['insurencename']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Activity :</div>
					<div class="controls">
						<?php 
						$activity = '';
						foreach ($data['page1']['activity'] as $avalue) {
							# code...
							$activity .= $avalue.',';
						}
						echo substr($activity,0,-1);

						?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Number of Performers :</div>
					<div class="controls">
					<?php 

						foreach ($this->perfomers as $pvalue) {
								# code...
								if($data['page1']['perfomers'] == $pvalue->price){
									// echo $pvalue->name;
									echo $data['page1']['perfomers'];
								}
							}
					?>
					</div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Estimated Annual Income :</div>
					<div class="controls">
					<?php 

						foreach ($this->annualincome as $aincome) {
								# code...
								if($data['page1']['annualincome'] == $aincome->price){
									echo $aincome->name;
								}
							}
					?>
					</div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Postcode :</div>
					<div class="controls"><?php echo $data['page1']['postcode']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Email Address :</div>
					<div class="controls"><?php echo $data['page1']['email']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Start Date :</div>
					<div class="controls"><?php echo $data['page1']['start_date']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Public Liability Cover :</div>
					<div class="controls"><?php if(strpos($data['page1']['liailitycover'],'$') !== False ){ echo $data['page1']['liailitycover']; }else{ echo $currencysymbol.number_format($data['page1']['liailitycover']);}  ?></div>
				</div>
	        </div>

	        <div id="Quotation" class="inactive">
	            <div class="control-group span12 first_row">
					<div class="control-label span3">Name of Insured :</div>
					<div class="controls"><?php echo $data['page1']['insurencename']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Activity :</div>
					<div class="controls">
						<?php 
						$activity = '';
						foreach ($data['page1']['activity'] as $avalue) {
							# code...
							$activity .= $avalue.',';
						}
						echo substr($activity,0,-1);

						?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Number of Performers :</div>
					<div class="controls">
					<?php 

						foreach ($this->perfomers as $pvalue) {
								# code...
								if($data['page1']['perfomers'] == $pvalue->price){
									// echo $pvalue->name;
									echo $data['page1']['perfomers'];
								}
							}
					?>
					</div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Estimated Annual Income :</div>
					<div class="controls">
					<?php 

						foreach ($this->annualincome as $aincome) {
								# code...
								if($data['page1']['annualincome'] == $aincome->price){
									echo $aincome->name;
								}
							}
					?>
					</div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Email Address :</div>
					<div class="controls"><?php echo $data['page1']['email']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Start Date :</div>
					<div class="controls"><?php echo $data['page1']['start_date']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Public Liability Cover :</div>
					<div class="controls"><?php if(strpos($data['page1']['liailitycover'], '$') !== False){ echo $data['page1']['liailitycover']; }else { echo $currencysymbol.number_format($data['page1']['liailitycover']); } ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Premium :</div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page2']['premium'],2); ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Administration Fee (Broker Fee) : </div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page2']['brokerfee'],2); ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Sub Total :</div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page2']['subtotal'],2); ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3"> GST Premium :</div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page2']['gstpremium'],2); ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Stamp Duty :</div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page2']['stampduty'],2); ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Administration Fee GST :</div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page2']['gstfee'],2); ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Total Premium :</div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page2']['totalpremium'],2); ?></div>
				</div>
				<!-- <div class="control-group span12">
					<div class="control-label span3">Commission :</div>
					<div class="controls"><?php echo $currencysymbol.$data['page2']['commission']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Commission GST :</div>
					<div class="controls"><?php echo $currencysymbol.$data['page2']['gstcommission']; ?></div>
				</div> -->
                                 <div class="control-group span12">
					<div class="control-label span3">Total Extras :</div>
					
						<?php 
						echo '<div class="controls span3">';
							foreach ($data['page6']['extrachreges'] as $key => $value) {
								echo $key.' : $'.number_format($value,2).'<br/>';
							}
						echo '</div>';
						?>
						
				</div>
	        </div>

	        <div id="Proposal" class="inactive">
	            <div class="control-group">
					<div class="control-label">Do you intend to hire out a performance venue to self-promote or stage your own performance?</div>
					<div class="controls">
						<?php 
						if($data['page3']['selfpromote'] == 'Y'){
							echo "Yes";
						}else if($data['page3']['selfpromote'] == 'N'){
							echo "No";
						}
						?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">Will you be signing any contracts that contain hold harmless or indemnity agreements?</div>
					<div class="controls">
					<?php 
						if($data['page3']['harmlessorindemnity'] == 'Y'){
							echo "Yes";
						}else if($data['page3']['harmlessorindemnity'] == 'N'){
							echo "No";
						}
						?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">Do you use subcontractors?</div>
					<div class="controls">
					<?php 
						if($data['page3']['subcontractors'] == 'Y'){
							echo "Yes";
						}else if($data['page3']['subcontractors'] == 'N'){
							echo "No";
						}
						?></div>
				</div>
				<div class="control-group">
					<div class="control-label">Have you previously been refused insurance or had your insurance cancelled by an insurer or have had special conditions, increased premiums or increased excesses imposed on any policy of insurance by an insurer?</div>
					<div class="controls">
					<?php 
						if($data['page3']['refusedinsurance'] == 'Y'){
							if($this->show == 'staff'){
								echo "<span style='color:red;'>Yes</span>";
							}else{
								echo "Yes";
							}
						}else if($data['page3']['refusedinsurance'] == 'N'){
							echo "No";
						}
						?></div>
				</div>
				<div class="control-group">
					<div class="control-label">Have you suffered any public liability claims or have caused incidents that give rise to public liability claim?</div>
					<div class="controls">
					<?php 
						if($data['page3']['liabilityclaim'] == 'Y'){
							if($this->show == 'staff'){
								echo "<span style='color:red;'>Yes</span>";
							}else{
								echo "Yes";
							}
						}else if($data['page3']['liabilityclaim'] == 'N'){
							echo "No";
						}
						?></div>
				</div>
				<div class="control-group">
					<div class="control-label">Have you been charged or convicted of a criminal offence (excluding driving convictions) in the last 10 years?</div>
					<div class="controls">
					<?php 
						if($data['page3']['criminaloffence'] == 'Y'){
							if($this->show == 'staff'){
								echo "<span style='color:red;'>Yes</span>";
							}else{
								echo "Yes";
							}
						}else if($data['page3']['criminaloffence'] == 'N'){
							echo "No";
						}
						?></div>
				</div>
				<div class="control-group">
					<div class="control-label">Audience participation with use of fire, sporting, hazardous or dangerous activities</div>
					<div class="controls">
					<?php 
						if($data['page3']['dangerousactivities'] == 'Y'){
							if($this->show == 'staff'){
								echo "<span style='color:red;'>Yes</span>";
							}else{
								echo "Yes";
							}
						}else if($data['page3']['dangerousactivities'] == 'N'){
							echo "No";
						}
						?></div>
				</div>
				<div class="control-group">
					<div class="control-label">Fireworks or pyrotechnics</div>
					<div class="controls">
					<?php 
						if($data['page3']['pyrotechnics'] == 'Y'){
							if($this->show == 'staff'){
								echo "<span style='color:red;'>Yes</span>";
							}else{
								echo "Yes";
							}
						}else if($data['page3']['pyrotechnics'] == 'N'){
							echo "No";
						}
						?></div>
				</div>
				<div class="control-group">
					<div class="control-label">Use of animals</div>
					<div class="controls">
					<?php 
						if($data['page3']['animals'] == 'Y'){
							if($this->show == 'staff'){
								echo "<span style='color:red;'>Yes</span>";
							}else{
								echo "Yes";
							}
						}else if($data['page3']['animals'] == 'N'){
							echo "No";
						}
						?></div>
				</div>
				<div class="control-group">
					<div class="control-label">Use of amusement rides or devices</div>
					<div class="controls">
					<?php 
						if($data['page3']['amusementrides'] == 'Y'){
							if($this->show == 'staff'){
								echo "<span style='color:red;'>Yes</span>";
							}else{
								echo "Yes";
							}
						}else if($data['page3']['amusementrides'] == 'N'){
							echo "No";
						}
						?></div>
				</div>
				<div class="control-group">
					<div class="control-label">Running of workshops</div>
					<div class="controls">
					<?php 
						if($data['page3']['workshops'] == 'Y'){
							if($this->show == 'staff'){
								echo "<span style='color:red;'>Yes</span>";
							}else{
								echo "Yes";
							}
						}else if($data['page3']['workshops'] == 'N'){
							echo "No";
						}
						?></div>
				</div>
				<div class="control-group">
					<div class="control-label">Any activities conducted in North America</div>
					<div class="controls"><?php 
						if($data['page3']['northamerica'] == 'Y'){
							if($this->show == 'staff'){
								echo "<span style='color:red;'>Yes</span>";
							}else{
								echo "Yes";
							}
						}else if($data['page3']['northamerica'] == 'N'){
							echo "No";
						}
						?></div>
				</div>	
	        </div>
	        
	        <div id="Address" class="inactive">
	            <div class="control-group span12 first_row">
					<div class="control-label span3">First Name :</div>
					<div class="controls"><?php echo $data['page4']['firstname']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Last Name :</div>
					<div class="controls"><?php echo $data['page4']['lastname']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Name of Insured :</div>
					<div class="controls"><?php echo $data['page4']['insuredname']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Street Address 1 :</div>
					<div class="controls"><?php echo $data['page4']['streetaddress1']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Street Address 2 :</div>
					<div class="controls"><?php echo $data['page4']['streetaddress2']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3"> Suburb :</div>
					<div class="controls"><?php echo $data['page4']['suburb']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">State/Territory :</div>
					<div class="controls">
						<?php 
						foreach ($this->statename as $key => $value) {
							if($value->id == $data['page4']['state']){
								echo $value->name;
							}	
						}
						?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Postcode :</div>
					<div class="controls"><?php echo $data['page4']['postcode']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Phone Number :</div>
					<div class="controls"><?php echo $data['page4']['phone']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Billing Address same as Policy Address : </div>
					<div class="controls"><?php echo $data['page4']['sameaddress']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Street Address 1 :</div>
					<div class="controls"><?php echo $data['page4']['billingaddress1']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Street Address 2 :</div>
					<div class="controls"><?php echo $data['page4']['billingaddress2']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Suburb :</div>
					<div class="controls"><?php echo $data['page4']['billingsuburb']; ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">State/Territory :</div>
					<div class="controls"><?php 
						foreach ($this->statename as $key => $value) {
							if($value->id == $data['page4']['billingstate']){
								echo $value->name;
							}	
						}
						?>
					</div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Postcode :</div>
					<div class="controls"><?php echo $data['page4']['billingpostcode']; ?></div>
				</div>
				<!-- <div class="control-group span12">
					<div class="control-label span3">Email Address :</div>
					<div class="controls"><?php echo $data['page4']['email']; ?></div>
				</div> -->
	        </div>

	        <div id="Declaration" class="inactive">
	           <div class="control-group">
					<div class="control-label">You acknowledge that you are entering into a legally binding contract for the provision of insurance cover and will settle all amounts due in accordance with the Terms and Conditions</div>
					<div class="controls"><?php if($data['page5']['termconditions'] == 1) { echo 'Yes'; }  ?></div>
				</div>
				<div class="control-group">
					<div class="control-label">You acknowledge that you have read the Financial Services Guide (FSG)</div>
					<div class="controls"><?php if($data['page5']['fsgread'] == 1) { echo "Yes"; }  ?></div>
				</div>
	        </div>

	        <div id="Checkout" class="inactive">
	        	<?php if($this->userdetail->activity_status == 'Active') { ?>
	        	<div class="control-group span12 first_row">
					<div class="control-label span3">Policy Number :</div>
					<div class="controls"><?php echo $this->userdetail->policy_number;?></div>
				</div>
				<?php } ?>
				<div class="control-group span12 first_row">
					<div class="control-label span3">Start Date :</div>
					<div class="controls"><?php echo $data['page6']['startdate'];?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Public Liability Cover :</div>
					<div class="controls"><?php if(strpos($data['page6']['publicliailitycover'],'$') !== False){ echo $data['page6']['publicliailitycover']; }else{ echo $currencysymbol.number_format($data['page6']['publicliailitycover']); } ?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Total Premium :</div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page6']['totalpremium'],2);?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Total Extras :</div>
					
						<?php 
						echo '<div class="controls span3">';
							foreach ($data['page6']['extrachreges'] as $key => $value) {
								echo $key.' : $'.number_format($value,2).'<br/>';
							}
						echo '</div>';
						?>
						
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Subtotal :</div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page6']['subtotal'],2);?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Credit Card Fee :</div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page6']['creditcardfee'],2);?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Order Total : </div>
					<div class="controls"><?php echo $currencysymbol.number_format($data['page6']['orderTotal'],2);?></div>
				</div>
				<div class="control-group span12">
					<div class="control-label span3">Payment By Credit Card :</div>
					<div class="controls"><?php if($data['page6']['creditcard'] == 'Y') { echo "Yes"; } ?></div>
				</div>
	        </div>
	    </div>
	</div>
		
</div>