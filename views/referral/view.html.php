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
class Event_manageViewReferral extends JViewLegacy
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
	public function display($tpl = null)
	{
		
		$formModel =  JModelLegacy::getInstance('forms','Event_manageModel');	
		$eventModel =  JModelLegacy::getInstance('event','Event_manageModel');	
		$session = JFactory::getSession();
		$user = & JFactory::getUser();		
		$app = JFactory::getApplication();		
		$user_id = $user->get( 'id' );
		$this->useremail = $user->get('email');
		$step = JRequest::getVar('step');
		$email = JRequest::getVar('email1');
		$insuredname = JRequest::getVar('insuredname');
		$status = JRequest::getVar('status');
		$this->sid = JRequest::getVar('sid');
		/*$hidden = JRequest::getVar('SpeakToAnExpert',array(), 'get', 'array');*/
		$jinput = JFactory::getApplication()->input;
		$config = JFactory::getConfig();
		$recipient = $config->get( 'mailfrom' );
		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->configemail = $componentParams->get('email');
		
		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');
        $formname = array('referral');
        $this->form = $formModel->getForm($formname);
        $this->pageArr = array('event'=>'1','quoatation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','checkout'=>'6');
        $this->pageKey = array('page1','page2','page3','page4','page5','page6','page7');

        	if($user_id){
				$this->userdata = $eventModel->getUserDataById($user_id);
				$this->progress = $eventModel->getSessionDataById( $this->userdata->id );
				$session->set('event_id', $this->userdata->id);
			}else{
				$this->progress = $eventModel->getSessionDataById($session->get('event_id'));
			}
		
		if($session->get('activereferral')){
			$this->refferal = $session->get('activereferral');
		}

		
		$this->PageOnedata = '';
		if($session->get('event_id', 'NULL') != 'NULL'){
			$this->PageOnedata = $session_model->getItem($session->get('event_id'));						
			$this->PageOnedata->data = unserialize($this->PageOnedata->data);				
		}      
		
		$this->AllPageData = '';
		if($session->get('allpagedata')){
			$this->AllPageData = $session->get('allpagedata');
		}
			
			$formdata = JRequest::getVar('jform');

			$this->form->bind($formdata);

			
			$this->newuserid = '';
			if ($_SERVER['REQUEST_METHOD'] !== 'POST'){	
				if($status == 'notfound'){
					if(empty($this->PageOnedata->data['page1']['insurencename'])){
						$this->PageOnedata->data['page1']['insurencename'] = $this->AllPageData['data']['page1']['insurencename'];
					}	
					if(!empty($email)){
						$this->PageOnedata->data['page1']['email'] = $email;
					}
					$session->set('savecomebackemail','1');
					$this->usercreate = $eventModel->setCreateNewUser($this->PageOnedata->data);
					$this->newuserid = $this->usercreate[2];
				}
				if($status == 'found'){
					/*$this->usercreate = $eventModel->getUserDetailByEmail($this->PageOnedata->data['page1']['email']);*/
					if(!empty($email)){
						$this->PageOnedata->data['page1']['email'] = $email;
					}
					$session->set('savecomebackemail','1');
					$this->usercreate = $eventModel->getUserDetailByEmail($this->PageOnedata->data['page1']['email']);
					$this->newuserid = $this->usercreate->id;
				}
			}
			

			$this->errors = '';
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {  

				if($this->form->validate($formdata)){	
					
					$data['onpage']= $this->AllPageData['onpage'];
					$data['create_date']= date('d-m-Y');
					foreach ($this->pageKey as $value) {
						# code...
						if(array_key_exists($value, $this->AllPageData['data'])){
							$data['data'][$value] = $this->AllPageData['data'][$value];		
						}
					}
						
			    	$data['status'] = '1';	
					$data['activity_status'] = 'Referral';
					$data['Uid'] = $formdata['userid'];
			    	$data['Eid'] = $session->get('event_id');


			    	if(!empty($this->sid)){
						$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
					}else{
						$ee = $eventModel->setEventsAndTerms($data);	    	
					} 
			    	/*$ee = $eventModel->setEventsAndTerms($data);*/
			    	
			    	if($formdata['SpeakToAnExpert']){
			    		
			    		/* check user exit or not if not exits then create new user */
			    		if(!empty($this->AllPageData['data']['page1']['email'])){
			    			$usercheck = $eventModel->getcheckUserName($this->AllPageData['data']['page1']['email']);
			    			
			    			if(!$usercheck){
						    	if(empty($this->AllPageData['data']['page1']['insurencename'])){
									$this->PageOnedata->data['page1']['insurencename'] = $this->AllPageData['data']['page1']['insurencename'];
								}	
								if(!empty($this->AllPageData['data']['page1']['email'])){
									$this->PageOnedata->data['page1']['email'] = $this->AllPageData['data']['page1']['email'];
								}
								// $session->set('savecomebackemail','1');
								$this->newusercreate = $eventModel->setCreateNewUser($this->PageOnedata->data);
								$data['Uid'] = $this->newusercreate[2];
								$ee = $eventModel->UpdateEventsAndTerms($data,$session->get('event_id'));
			    			}
			    		}
			    		/* send notification email to admin (start here)*/
						$adminbody = "";
						$adminbody .= '<div> Hello Admin,</div><br/>';
						$adminbody .= '<div>There is a customer requires speak to an expert.<br/>Below are the details of customer.<br/><br/>';
						$adminbody .= '<div>Order Number: '. $session->get('event_id') .'</div>';
						$adminbody .= '<div>Name of Insured: '.$this->AllPageData['data']['page1']['insurencename'].'</div>';
						$adminbody .= '<div>Email Address: '.$this->AllPageData['data']['page1']['email'].'</div>';
						$adminbody .= '<br/>Please check the staff dashboard for more details.<br/>';
						$adminbody .= '<div>Thanks & Regards<br/>';
						$adminbody .= 'Action Entertainment Insurance</div>';
						$adminmailData = array();
						$adminmailData['subject'] = "Notifiaction - Speak to an Expert";
						$adminmailData['sender'] = $this->AllPageData['data']['page1']['email'];
						if($this->configemail){
							$adminmailData['recipient'] = $this->configemail;
						}else{
							$adminmailData['recipient'] = $recipient;	
						}
						$adminmailData['body'] = $adminbody;
						
						$send = $this->_sendMail($adminmailData, TRUE);

						/* End Here  */

						/* send notification email to client (start here) */
						$clientbody = "";
						$clientbody .= '<div> Hello User,</div><br/>';
						$clientbody .= '<div>Thank you for submission,';
						$clientbody .= 'one of our staff will be in touch with you shortly</div><br/><br/>';
						$clientbody .= '<div>Thanks & Regards<br/>';
						$clientbody .= 'Action Entertainment Insurance</div>';
						$clientmailData = array();
						$clientmailData['subject'] = "Confirm - Speak to an Expert";
						$clientmailData['recipient'] = $this->AllPageData['data']['page1']['email'];
						if($this->configemail){
							$clientmailData['sender'] = $this->configemail;
						}else{
							$clientmailData['sender'] = $recipient;	
						}
						$clientmailData['body'] = $clientbody;

						$send = $this->_sendMail($clientmailData, TRUE);

						/* End Here */

						if($send){
							JFactory::getApplication()->enqueueMessage('Page third store succesfully');	
							$url = JRoute::_('index.php?option=com_event_manage&view=event');
							$session->destroy(); 
							$app->redirect($url);
						}
					}	    	

					if($ee === 'update'){
						$sendmail = $session->get('savecomebackemail');
						 if($sendmail){
					    	$body  = "<div>Detail about your policy: </div></br></br>";
					    	$body .= "<div><table border='1'>";
							$body .= "<tr>";
							$body .= "<th align='left'>Order No:</th>";
							$body .= "<td>". $session->get('event_id') ."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Insurence Name:</th>";
							$body .= "<td>".$this->AllPageData['data']['page1']['insurencename']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Activity:</th>";
							$activity = '';
							foreach ($this->AllPageData['data']['page1']['activity'] as $avalue) {
								$activity .= $avalue.',';
							}
							$activity = substr($activity,0,-1);
							$body .= "<td>".$activity."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Number of Performers:</th>";
							$body .= "<td>".$this->AllPageData['data']['page1']['performers']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Estimated Annual Income:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page1']['annualincome']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Postcode:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page1']['postcode']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Start Date:</th>";
							$body .= "<td>".$this->AllPageData['data']['page1']['start_date']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Email:</th>";
							$body .= "<td>".$this->AllPageData['data']['page1']['email']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Public Liability Cover:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page1']['liailitycover']."</td>";
							$body .= "</tr>";
							if(array_key_exists('page2', $this->AllPageData['data'])){
								$body .= "<tr>";
								$body .= "<th align='left'>Premium:</th>";
								$body .= "<td>$".$this->AllPageData['data']['page2']['premium']."</td>";
								$body .= "</tr>";
								$body .= "<tr>";
								$body .= "<th align='left'>Broker Fee:</th>";
								$body .= "<td>$".$this->AllPageData['data']['page2']['brokerfee']."</td>";
								$body .= "</tr>";
								$body .= "<tr>";
								$body .= "<th align='left'>GST Premium:</th>";
								$body .= "<td>$".$this->AllPageData['data']['page2']['gstpremium']."</td>";
								$body .= "</tr>";
								$body .= "<tr>";
								$body .= "<th align='left'>Stamp Duty:</th>";
								$body .= "<td>$".$this->AllPageData['data']['page2']['stampduty']."</td>";
								$body .= "</tr>";
								$body .= "<tr>";
								$body .= "<th align='left'>GST Fee:</th>";
								$body .= "<td>$".$this->AllPageData['data']['page2']['gstfee']."</td>";
								$body .= "</tr>";
								$body .= "<tr>";
								$body .= "<th align='left'>Sub Total:</th>";
								$body .= "<td>$".$this->AllPageData['data']['page2']['subtotal']."</td>";
								$body .= "</tr>";
								$body .= "<tr>";
								$body .= "<th align='left'>Total Premium:</th>";
								$body .= "<td>$".$this->AllPageData['data']['page2']['totalpremium']."</td>";
								$body .= "</tr>";
								$body .= "<tr>";
								$body .= "<th align='left'>Commission:</th>";
								$body .= "<td>$".$this->AllPageData['data']['page2']['commission']."</td>";
								$body .= "</tr>";
								$body .= "<tr>";
								$body .= "<th align='left'>GST Commission:</th>";
								$body .= "<td>$".$this->AllPageData['data']['page2']['gstcommission']."</td>";
								$body .= "</tr>";
							}
							$body .= "</table></div><br>";
							$body .= '<div>Thank & Regards<br>';
							$body .= 'Action Entertainment Insurance</div><br>';
					    	$mailData = array();
							$mailData['subject'] = "Confirm - Save And Come Back Later";
							$mailData['sender'] = $this->AllPageData['data']['page1']['email'];
							if($this->configemail){
								$mailData['recipient'] = $this->configemail;
							}else{
								$mailData['recipient'] = $recipient;	
							}
							$mailData['body'] = $body;
							$send = $this->_sendMail($mailData, TRUE);

						}
				     	
				    	$session->clear('savecomebackemail');
						JFactory::getApplication()->enqueueMessage('Page two store succesfully');	
						$url = JRoute::_('index.php?option=com_event_manage&view=event');
						$session->destroy(); 
						$app->redirect($url);
					}
			    		    	
					if($ee != false ){
						$sendmail = $session->get('savecomebackemail');
					 if($sendmail){
				    	$body  = "<div>Detail about your policy: </div></br>";
				    	$body .= "<div><table border='1'>";
						$body .= "<tr>";
						$body .= "<th align='left'>Order No:</th>";
						$body .= "<td>". $session->get('event_id') ."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Insurence Name:</th>";
						$body .= "<td>".$this->AllPageData['data']['page1']['insurencename']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Activity:</th>";
						$activity = '';
						foreach ($this->AllPageData['data']['page1']['activity'] as $avalue) {
							$activity .= $avalue.',';
						}
						$activity = substr($activity,0,-1);
						$body .= "<td>".$activity."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Number of Performers:</th>";
						$body .= "<td>$".$this->AllPageData['data']['page1']['performers']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Estimated Annual Income:</th>";
						$body .= "<td>$".$this->AllPageData['data']['page1']['annualincome']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Postcode:</th>";
						$body .= "<td>$".$this->AllPageData['data']['page1']['postcode']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Start Date:</th>";
						$body .= "<td>".$this->AllPageData['data']['page1']['start_date']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Email:</th>";
						$body .= "<td>".$this->AllPageData['data']['page1']['email']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Public Liability Cover:</th>";
						$body .= "<td>$".$this->AllPageData['data']['page1']['liailitycover']."</td>";
						$body .= "</tr>";
						if(array_key_exists('page2', $this->AllPageData['data'])){
							$body .= "<tr>";
							$body .= "<th align='left'>Premium:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page2']['premium']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Broker Fee:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page2']['brokerfee']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>GST Premium:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page2']['gstpremium']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Stamp Duty:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page2']['stampduty']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>GST Fee:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page2']['gstfee']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Sub Total:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page2']['subtotal']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Total Premium:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page2']['totalpremium']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Commission:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page2']['commission']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>GST Commission:</th>";
							$body .= "<td>$".$this->AllPageData['data']['page2']['gstcommission']."</td>";
							$body .= "</tr>";
						}
						$body .= "</table></div><br>";
						$body .= '<div>Thank & Regards<br>';
						$body .= 'Action Entertainment Insurance</div><br>';
				    	$mailData = array();
						$mailData['subject'] = "Confirm - Save And Come Back Later";
						$mailData['sender'] = $this->AllPageData['data']['page1']['email'];
						if($this->configemail){
							$mailData['recipient'] = $this->configemail;
						}else{
							$mailData['recipient'] = $recipient;	
						}
						$mailData['body'] = $body;
						$send = $this->_sendMail($mailData, TRUE);
						
					}
				     	
				    $session->clear('savecomebackemail');
						JFactory::getApplication()->enqueueMessage('Page third store succesfully');	
						$url = JRoute::_('index.php?option=com_event_manage&view=address&step=4');
						$app->redirect($url);

					}
				}else{
					
					$this->errors = '<ul class="server_error">';
					
					foreach ($this->form->getErrors() as $key => $value) {
						$this->errors .= '<li>'.$value->getMessage().'</li>';
					}				
					$this->errors .= '</ul>';
				}				
			}
			
			$this->frontdata = '';
			if($session->get('event_id', 'NULL') != 'NULL' || $user_id){
				$this->frontdata = $session_model->getItem($session->get('event_id'));						
				$this->frontdata->data = unserialize($this->frontdata->data);
				if($user_id != $this->frontdata->user){
					$session->get('event_id',NULL);
					$data = NULL;
				}			
			}
		parent::display($tpl);					
		
	}

	public function _sendMail($mailData, $isHtml = FALSE){
		$sender = $mailData['sender'];
		$recipient = $mailData['recipient'];
		$body = $mailData['body'];
		$subject = $mailData['subject'];

		$mailer = JFactory::getMailer();
		$mailer->setSender($sender);
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->isHTML($isHtml);
		$mailer->setBody($body);
		return $mailer->Send();
	}

}