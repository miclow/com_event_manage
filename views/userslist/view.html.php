<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Event_manage
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

jimport( 'joomla.form.form' );

/**
 * View to edit
 *
 * @since  1.6
 */
class Event_manageViewUserslist extends JViewLegacy
{
	
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */

	protected $pagination;


	public function display($tpl = null)
	{
		
		$formModel =  JModelLegacy::getInstance('forms','Event_manageModel');	
		$eventModel =  JModelLegacy::getInstance('event','Event_manageModel');	
		$session = JFactory::getSession();
		$user = & JFactory::getUser();		
		$app = JFactory::getApplication();		
		$user_id = $user->get( 'id' );
		$pid = JRequest::getVar('pid');
		$thsi->groups = JUserHelper::getUserGroups($user_id);
		$this->s_ordernumber = JRequest::getVar('s_ordernumber','','post');
		$this->s_policynumber = JRequest::getVar('s_policynumber','','post');
		$this->s_firstname = JRequest::getVar('s_firstname','','post');
		$this->s_lastname = JRequest::getVar('s_lastname','','post');
		$this->s_email = JRequest::getVar('s_email','','post');
		$this->orderno = JRequest::getVar('orderno');
		$this->onpage = JRequest::getVar('onpage');
		$this->file = JRequest::getVar('file');


		if(!empty($this->file)){
			$this->_download($this->file);
		}

		$jinput = JFactory::getApplication()->input;

		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->configemail = $componentParams->get('email');

		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');

        $formname = array('quotation');
        $this->form = $formModel->getForm($formname);
        $this->pageArr = array('event'=>'1','quotation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','checkout'=>'6');
		
		if(isset($pid)){
			
			$ee = $eventModel->updateUnpaidToPaid($pid);

			if($ee != false ){
				
				$userdata = $eventModel->getSessionDataById($pid);
				/* Gernate Policy PDF File */
				$res = $eventModel->gernatePDF($userdata);
				/* Gernate Policy certificate PDF File */
				$certificate = $eventModel->gernateCertificatPDF($userdata);
				/* Gernate Policy Questionnaire Summery PDF File */
				$questionnaire = $eventModel->gernateQuestonnairePDF($userdata);
				$pathArr = explode('/', $res);
				$filename = $pathArr[count($pathArr)-1]; 
				$filenamesave = $eventModel->savepdffilename($pid,$filename);
				$data = unserialize($userdata->data);	
				if(!empty($data['page4']['email'])){
					$recipient = $data['page4']['email'];
				} else {
					$recipient = $data['page1']['email'];
				}

				$this->userperfomers = $eventModel->getTerm('Perfomers');
				$this->userExtracharegs = $eventModel->getTerm('Excharge');
				$this->userannualincome = $eventModel->getTerm('AnnualIncome');
				$this->UserExtraCharegsByOrderId = $eventModel->getExtraFieldByOrder($userdata->id);

				$ucharges = 0;
				if(!empty($this->userExtracharegs)){
					$Extrachreges = $this->userExtracharegs;
					foreach ($Extrachreges as $evalue) {
						$ucharges += $evalue->price;
					}
				}

				if(!empty($this->UserExtraCharegsByOrderId)){
					$ChregesByOrderId = $this->UserExtraCharegsByOrderId;
					foreach ($ChregesByOrderId as $cvalue) {
						$ucharges += $cvalue->price;
					}
				}
				
				$TotalExtras = round($ucharges,2);

				$componentParams = &JComponentHelper::getParams('com_event_manage');
				$this->chargecurrency = $componentParams->get('chargecurrency');
				$this->insurername = $componentParams->get('insurername');
				$this->insureraddress = $componentParams->get('insureraddress');
				$this->abn = $componentParams->get('abn');

				$body = "";
				$body .= "<div>Check mail for unpaid to paid by manully</div><br/>";
				$body .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#8d8e90">';
				$body .= '<tr>';
				$body .= '<td>';
				$body .= '<table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">';
				$body .= '<tr>';
				$body .= '<td align="center">';
				$body .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
				$body .= '<tr>';
				$body .= '<td>';
				$body .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
				$body .= '<tr>';
				$body .= '<td height="46" align="right" valign="middle">';
				$body .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
				$body .= '<tr>';
				$body .= '<td width="67%" align="right">';
				$body .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
				$body .= '<tr>';
				$body .= '<td align="right"><a href="https://www.entertainmentinsurance.net.au/"><img src="'.JURI::base().'images/new_logo.png" width="598" height="186" alt="Action Entertainment" /></a></td>';
				$body .= '</tr>';
				$body .= '</table>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '</table>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '</table>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '</table>';
				$body .= '<font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#68696a; font-size:22px; text-transform:uppercase"><strong>confirmation email</strong></font>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '<tr>';
				$body .= '<td>';
				$body .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
				$body .= '<tr>';
				$body .= '<td width="7%">&nbsp;</td>';
				$body .= '<td width="100" align="left" valign="top">';
				$body .= '<table width="93%" border="0" cellspacing="0" cellpadding="0">';
				$body .= '<tr>';
				$body .= '<td style="font-size: 12px;"><p>Dear '.ucfirst($data['page4']['firstname']).',</p>';
				$body .= '<p>Thank you for  purchasing your insurance with Action Entertainment Insurance. <br />';
				$body .= 'We have  pleasure enclosing the following documents which confirm cover: </p>';
				$body .= '<ul>';
				$body .= '<li>Tax Invoice &amp; Policy Schedule</li>';
				$body .= '<li>Policy Wording</li>';
				$body .= '<li>Certificate of Currency</li>';
				$body .= '<li>Questionnaire Response Summary</li>';
				$body .= '</ul>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '<tr>';
				$body .= '<td>&nbsp;</td>';
				$body .= '</tr>';
				$body .= '<tr>';
				$body .= '<td align="left" valign="top">';
				$body .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
				$body .= '<tr>';
				$body .= '<td width="45%" style="font-size: 12px;"><p><strong>POLICY DETAILS</strong><br />
				          Policy Number:<br />
				          Order No:<br />
				          Name of  Insured:<br />
				          Activities:<br />
				          Number of  Performers:<br />
				          Estimated  Annual Income:<br />
				          Public  Liability Cover:<br />
				          Postcode:<br />
				          Start Date:<br />
				          Email:</p>';
				$body .= '</td>';
				$body .= '<td width="55%" style="font-size: 12px;"><p><br />
				          '.$userdata->policy_number.'<br />
				          '.$userdata->id.'<br />
				          '.$data['page1']['insurencename'].'<br />';
				          $activity = '';
				          foreach ($data['page1']['activity'] as $avalue) {
				           $activity .= $avalue.', ';
				          }
				$body .= substr($activity,0,-2);
				$body .='<br />';
				          foreach ($this->userperfomers as $pvalue) {
					          if($data['page1']['perfomers'] == $pvalue->price){
					          $body .= $pvalue->price;
					          }
				          }
				$body .= '<br />';
				          foreach ($this->userannualincome as $aincome) {
					          if($data['page1']['annualincome'] == $aincome->price){
					          $body .= $aincome->name;
					          }
				          }
				$body .= '<br />';
				          if(!empty($data['page1']['liailitycover']) && (strpos($data['page1']['liailitycover'],'$') !== False)){
							 $publicliability = $data['page1']['liailitycover'];
						  }else if(!empty($data['page1']['liailitycover'])){
							$publicliability ='$'.number_format($data['page1']['liailitycover']);
						  }
				$body .= $publicliability;
				$body .= '<br />
				          '.$data['page1']['postcode'].'<br />
				          '.$data['page1']['start_date'].'<br />
				          '.$data['page1']['email'].'<br />';
				$body .= '</p>
				          </td>
				          </tr>
				          <tr>
				          <td width="45%">&nbsp;</td>
				          <td width="55%">&nbsp;</td>
				          </tr>
				          <tr>
				          <td valign="top" style="font-size: 12px;"><p><strong>FEES</strong><br />
				          Premium: <br />
				          GST Premium:<br />
				          Stamp Duty:<br />
				          Broker Fee:<br />
				          GST Fee:</p>
				          <p><strong>Total Premium:</strong><br />';
				          if(!empty($TotalExtras)){ 
				            $body .= '  Total Extras Charge:<br />Sub Total:<br />';
				          }
				$body .= 'Credit Card  Fee:<br />
				          Order Total:</p>
				          </td>
				          <td valign="top" style="font-size: 12px;"><p><br />
				          $'.number_format($data['page2']['premium'],2).'<br />
				          $'.number_format($data['page2']['gstpremium'],2).'<br />
				          $'.number_format($data['page2']['stampduty'],2).'<br />
				          $'.number_format($data['page2']['brokerfee'],2).'<br />
				          $'.number_format($data['page2']['gstfee'],2).'</p>
				          <p><strong>$';
				          $totalpremium = number_format($data['page2']['premium'],2)+number_format($data['page2']['gstfee'],2)+number_format($data['page2']['brokerfee'],2)+number_format($data['page2']['stampduty'],2)+number_format($data['page2']['gstpremium'],2);
							$body .= number_format($totalpremium,2);
							$body .= '</strong><br />';
				          if(!empty($TotalExtras)){ 
							$body .= '$'.number_format($TotalExtras,2).'<br />';
							$body .= '$'.number_format($data['page6']['subtotal'],2).'<br />';
				          }
				          $body .= '$'.number_format($data['page6']['creditcardfee'],2).'<br />
				          $'.number_format($data['page6']['orderTotal'],2).'</p>';
				$body .= '<p><br />';
				$body .= '</p>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '</table>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '</table>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '<td width="7%">&nbsp;</td>';
				$body .= '</table>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '<tr>';
				$body .= '<td style="font-size: 12px;"><p align="center"><strong>ACTION ENTERTAINMENT INSURANCE  PTY LTD<br />
				           </strong>Corporate Representative No. 237473 as an  authorised representative of Action Insurance Brokers P/L<br />
				           ABN '.$this->abn.' AFS 225047<br />
				           Suite 301, Building  A, &ldquo;Sky City&rdquo;, 20 Lexington Drive Bella Vista NSW 2153<br />
				           Phone: 1300 655 424<strong> </strong>Fax: (02) 8935 1501<br />
				           Website: <a href="http://www.entertainmentinsurance.net.au">www.entertainmentinsurance.net.au</a> <br />
				           Email: <a href="mailto:entertainment@actioninsurance.com.au">entertainment@actioninsurance.com.au</a></p></td>';
				$body .= '</tr>';
				$body .= '<tr><td>&nbsp;</td></tr>';
				$body .= '<tr>';
				$body .= '<td>';
				$body .= '<img src="../../Free Email Templates - 99designs/Free Email Templates - 99designs/Green/NEWSLETTER GREEN/images/PROMO-GREEN2_07.jpg" width="598" height="7" style="display:block" border="0" alt=""/>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '<tr><td>&nbsp;</td></tr>';
				$body .= '<tr>';
				$body .= '<td>';
				$body .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
				$body .= '<tr>';
				$body .= '<td width="13%" align="center">&nbsp;</td>';
				$body .= '<td width="14%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://www.entertainmentinsurance.net.au/" style="color:#010203; text-decoration:none"><strong>ABOUT</strong></a><a href= "http://yourlink" style="color:#010203; text-decoration:none"></a></font></td>';
				$body .= '<td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>';
				$body .= '<td width="9%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://www.entertainmentinsurance.net.au/products" style="color:#010203; text-decoration:none"><strong>PRODUCTS</strong></a><a href= "https://www.entertainmentinsurance.net.au/" style="color:#010203; text-decoration:none"></a></font></td>';
				$body .= '<td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>';
				$body .= '<td width="10%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://www.entertainmentinsurance.net.au/claims" style="color:#010203; text-decoration:none"><strong>CLAIMS </strong></a></font></td>';
				$body .= '<td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>';
				$body .= '<td width="11%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://www.entertainmentinsurance.net.au/contact-us" style="color:#010203; text-decoration:none"><strong>CONTACT </strong></a></font></td>';
				$body .= '<td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>';
				$body .= '<td width="17%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://yourlink" style="color:#010203; text-decoration:none"><strong>STAY CONNECTED</strong></a></font></td>';
				$body .= '<td width="4%" align="right"><a href="https://www.facebook.com/ActionEntertainmentInsurance" target="_blank"><img src="'.JURI::base().'images/facebook.jpg" width="23" height="19" border="0" /></a></td>';
				$body .= '<td width="5%" align="right"><a href="https://twitter.com/EntertainInsure" target="_blank"><img src="'.JURI::base().'images/twitter.jpg" alt="twitter" width="27" height="19" border="0" /></a></td>';
				$body .= '<td width="4%" align="right"><a href="https://www.linkedin.com/company/action-entertainment-insurance-pty-ltd" target="_blank"><img src="'.JURI::base().'images/linkedin.jpg" width="25" height="19" border="0" /></a></td>';
				$body .= '<td width="5%">&nbsp;</td>';
				$body .= '</tr>';
				$body .= '</table>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '<tr><td>&nbsp;</td></tr>';
				$body .= '</table>';
				$body .= '</td>';
				$body .= '</tr>';
				$body .= '</table>';
				$mailData = array();
				$mailData['subject'] = "Thank you for purchasing your insurance with us";
				$mailData['recipient'] = $recipient;
				$mailData['body'] = $body;
				$mailData['attachment'] = $res;
				/* Policy Wording pdf file attachement when payment made success */
				
				/* Old Pdf */
				// $PolicyWordingFileName = 'Policy_Wording.pdf';
				/* New Pdf */
				$PolicyWordingFileName = 'PERFORMSURE_POLICY_WORDING_BIA_GL_G2_Arena_Ent_1_2016.pdf';
				$PolicyWordingFilePath = JPATH_ROOT.'/'.'components/pdf/'.$PolicyWordingFileName;
				$mailData['attachment1'] = $PolicyWordingFilePath;
				$mailData['attachment2'] = $certificate;
				$mailData['attachment3'] = $questionnaire;
				/* Send Email With Attached File */
				$send = $this->_sendMail($mailData, TRUE);
				/* Gernate XML File */
				$resultxml = $eventModel->gernateXML($userdata);
				$url = JRoute::_('index.php?option=com_event_manage&view=userslist');
				$app->redirect($url);
			}
		}


		$formdata = JRequest::getVar('jform');
		$this->form->bind($formdata);
		$this->errors = '';
	
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!empty($formdata['orderid']) || !empty($this->orderid))){
			
			if($this->form->validate($formdata)){	
				
				if(!empty($formdata['name'])){
					$formdata['name'] = $formdata['name'];
				}else{
					$formdata['name'] = $formdata['name1'];
				}

				if(!empty($formdata['price'])){
					$formdata['price'] = $formdata['price'];
				}else{
					$formdata['price'] = $formdata['price1'];
				}

				if(!empty($formdata['description'])){
					$formdata['description'] = $formdata['description'];
				}else{
					$formdata['description'] = $formdata['description1'];
				}
				
				$data['state'] = '1';
				$data['created_by'] = $user_id;
				$data['create_date']= date('d-m-Y');
				$data['description'] = $formdata['description'];
				if($this->orderno){
					$data['order_id'] = $this->orderno;	
					$data['onpage'] = $this->onpage;
					$orderid = $this->orderno;
					$onpage = $this->onpage;
				}else{
					$data['order_id'] = $formdata['orderid'];
					$data['onpage'] = $formdata['onpage'];	
					$orderid = $formdata['orderid'];
					$onpage = $formdata['onpage'];
				}

				if($formdata['Excharge'] == 'Excharge'){
					$data['name'] = $formdata['name'];
					$data['price'] = $formdata['price'];
					$data['activity'] = $formdata['Excharge'];
				}

				$firstpagedata = $eventModel->getSessionDataById($orderid);
				$ReffralPageArr = unserialize($firstpagedata->data);
				if($data['onpage'] != 3){
					if($ReffralPageArr['page1']['perfomers'] == '13+' && $ReffralPageArr['page1']['annualincome'] == '$250,000+'){
						$ExtraFieldExits = $eventModel->getNumberOfExtraFieldOrderWise($data['order_id'],$data['onpage'],$ReffralPageArr['page1']['perfomers']);
						if(empty($ExtraFieldExits)){
							$data['numberofreffral'] = 2;
						}
					}
				}
				

				if($formdata['performers'] == '13+'){
					$data['premium'] = $formdata['premium'];
					$data['brokerfee'] = $formdata['brokerfee'];
					$data['activity'] = 'Excharge';	
					$pageone = array('premium','brokerfee');
				}

				if($firstpagedata->activity_status == 'Referral'){
					$data['activity_status'] = 'Referral';
				}else if($firstpagedata->activity_status == 'Hold'){
					$data['activity_status'] = 'Hold';
				}else{
					$data['activity_status'] = $firstpagedata->activity_status;
				}
				// die;				
				
				$redirectnew = 0;
				if(!empty($formdata['savenew'])){
						$redirectnew = 1;					
				}
					if(count($pageone) > 0){
						for($i=0; $i<count($pageone); $i++){

								if($pageone[$i] == 'premium' ){
									$data['name'] = 'premium';
									$data['price'] = $data['premium'];
									$ee = $eventModel->addExtraFieldWithOrderId($data);
								}
								if($pageone[$i] == 'brokerfee' ){
									$data['name'] = 'brokerfee';
									$data['price'] = $data['brokerfee'];
									$ee = $eventModel->addExtraFieldWithOrderId($data);
								}
						}
					}else{
						$ee = $eventModel->addExtraFieldWithOrderId($data);
					}	
				if($ee != false ){


					JFactory::getApplication()->enqueueMessage('Extra field added for this order-no '.$orderid);	
					if($redirectnew){
						$url = JRoute::_('index.php?option=com_event_manage&view=userslist&orderno='.$orderid.'&onpage='.$onpage.'#openModalReffralThird');
					}else if($data['numberofreffral']){
						$url = JRoute::_('index.php?option=com_event_manage&view=userslist&orderno='.$orderid.'&onpage='.$onpage.'#openModalReffralThird');
					}else{
						$sendemail = 1;
						$url = JRoute::_('index.php?option=com_event_manage&view=userslist');	
					}
					
					if($sendemail){
						$mailuserdata = $eventModel->getSessionDataById($orderid);
						$emaildata = unserialize($mailuserdata->data);	
						if(!empty($emaildata['page4']['email'])){
							$mailrecipient = $emaildata['page4']['email'];
						} else {
							$mailrecipient = $emaildata['page1']['email'];
						}
						// if($emaildata['page1']['perfomers'] == '13+'){
						// 	$perfomers = '13+';
						// }
						$this->extrpremium = $eventModel->getExtraFieldOrderWise($orderid);

						$body = "Hi,<br><br>";
                                                $body .= "You recently filled in our Online Form for Performsure.<br>";
                                                $body .= "When filling in the Proposal Section you answered YES to one or more questions which triggered a referral to one of our consultants (Speak to An Expert button).<br>";
                                                $body .= "We have reviewed your application, applying the following Extra Charges<br><br>";
						$body .= '<div><table border="1">';
						$body .= '<tbody>';
						$body .= '<tr>';
						$body .= '<th align="left">';
						$body .= 'Description:';
						$body .= '</th>';
						$body .= '<th align="left">';
						$body .= 'Amount';
						$body .= '</th>';
						$body .= '</tr>';
						foreach ($this->extrpremium as $evalue) {
							$body .= '<tr>';
							$body .= '<td>';
							$body .= $evalue->name;
							$body .= '</td>';
							$body .= '<td>';
							$body .= $evalue->price;
							$body .= '</td>';
							$body .= '</tr>';
						}
						$body .= '</tbody>';
						$body .= '</table></div><br/><br />';
                                                $body .= 'Please log into your account and perform the following steps to complete the activation of your new policy<br><br>';
                                                $body .= '<ul><li>Once logged in, click on Edit</li>';
                                                $body .= '<li>Accept the Terms & Conditions</li>';
                                                $body .= '<li>You will see your new Extra Charge/s listed</li>';
                                                $body .= '<li>Enter your Credit Card details to pay</li></ul><br><br>';
						$body .= '<div>Regards</div><br/>';
						$body .= '<div>Action Entertainment Insurance</div>';
						
						$mailData = array();
						$mailData['subject'] = "Action Entertainment – Performsure Online Quotation – Extra Charges";
						$mailData['recipient'] = $mailrecipient;
						$mailData['body'] = $body;
						if(empty($data['numberofreffral'])){
							$send = $this->_sendMail($mailData, TRUE);
						}
					}

					$app->redirect($url);
				}
		    		
		  //   		$mesgArr = '';
			 //        foreach ($this->referralArr as $rvalue){
				//     	if($formdata[$rvalue] == 'Y'){
				//     		$mesgArr[] = $rvalue;
				//     	}
			 //    	}
			    	
			 //    	if(!empty($this->sid)){
				// 		$sid = '&sid='.$this->sid;
				// 	}

			 //    	foreach ($this->referralArr as $rvalue) {
				// 		if($formdata[$rvalue] == 'Y'){
				// 			$session->set('allpagedata', $data);
				//     		$session->set('referraldata', $formdata);
				//     		$session->set('activereferral', $mesgArr);
				// 		$url = JRoute::_('index.php?option=com_event_manage&view=referral&step=6b'.$sid);
				// 		$app->redirect($url);
				//     	}
				// 	}
				 
		  //   		$data['Uid'] = $formdata['userid'];
			 //    	$data['Eid'] = $session->get('event_id');
		    	
			 //    /*$ee = $eventModel->setEventsAndTerms($data);*/	
			 //    if(!empty($this->sid)){
				// 	$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
				// }else{
				// 	$ee = $eventModel->setEventsAndTerms($data);	    	
				// }    	

				// if($ee === 'update'){
				// 	JFactory::getApplication()->enqueueMessage('Page third store succesfully');	
				// 	$url = JRoute::_('index.php?option=com_event_manage&view=event');
				// 	$session->destroy(); 
				// 	$app->redirect($url);
				// }
						    	
				// if($ee != false ){
				// 	JFactory::getApplication()->enqueueMessage('Page third store succesfully');	
				// 	$url = JRoute::_('index.php?option=com_event_manage&view=address&step=4'.$sid);
				// 	$app->redirect($url);

				// }
			}else{
				
				$this->errors = '<ul class="server_error">';
				
				foreach ($this->form->getErrors() as $key => $value) {
					$this->errors .= '<li>'.$value->getMessage().'</li>';
				}				
				$this->errors .= '</ul>';
			}

			/*$this->events = $eventModel->setEventsAndTerms($data);*/						
		}

		$this->userdetail = $eventModel->getAllUserDetail();
		$this->gst = $eventModel->getTerm('GST');
		if(!$user_id){
			$url = JRoute::_('index.php?option=com_event_manage&view=event');
			$app->redirect($url);
		}
		$autorizedid = array('6','8');
		foreach ($thsi->groups as $gid){
			if(!in_array($gid, $autorizedid)){
				foreach ($this->pageArr as $key => $value){
					if($value == $this->userdetail->onpage){		
						$step = $value; 									
						$url = JRoute::_('index.php?option=com_event_manage&view='.$key.'&step='.$step);
						$app->redirect($url);
					}else{
						$url = JRoute::_('index.php?option=com_event_manage&view=event');
						$app->redirect($url);
					}
				}
			}
		}
		
		$this->pagination = $eventModel->getPagination();
		$this->AllExtraField = $eventModel->getDistinctExtraField();
		// echo "<pre>";
		// print_r($this->AllExtraField);
		// echo "</pre>";
		parent::display($tpl);					
	}

	public function _sendMail($mailData, $isHtml = FALSE){
			
		$recipient = $mailData['recipient'];
		$body = $mailData['body'];
		$subject = $mailData['subject'];
		$config = JFactory::getConfig();
		$sender = array( 
		    $config->get( 'mailfrom' ),
		    $config->get( 'fromname' ) 
		);
		$mailer = JFactory::getMailer();
	
		if($this->configemail){
			$sender = array($this->configemail,$config->get( 'fromname' ));	
		}
		$attachment = $mailData['attachment'];
		$attachment1 = $mailData['attachment1'];
		$attachment2 = $mailData['attachment2'];
		$attachment3 = $mailData['attachment3'];
		$mailer->setSender($sender);
		$mailer->addRecipient($recipient);
		if(!empty($attachment)){
			if(!empty($attachment1) && !empty($attachment2) && !empty($attachment3)){
				$mailer->addAttachment(array($attachment,$attachment1,$attachment2,$attachment3));
			}else if(!empty($attachment1) && !empty($attachment2)){
				$mailer->addAttachment(array($attachment,$attachment1,$attachment2));
			}else if(!empty($attachment1)){
				$mailer->addAttachment(array($attachment,$attachment1));
			}else{
				$mailer->addAttachment($attachment);
			}
		}
		$mailer->setSubject($subject);
		$mailer->isHTML($isHtml);
		$mailer->setBody($body);
		return $mailer->Send();
	}

	public function _download($file){

		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=" . urlencode($file));   
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Description: File Transfer");            
		header("Content-Length: " . filesize($file));
		flush(); // this doesn't really matter.
		$fp = fopen($file, "r");
		while (!feof($fp))
		{
		    echo fread($fp, 65536);
		    flush(); // this is essential for large downloads
		} 
		fclose($fp); 
	}
}