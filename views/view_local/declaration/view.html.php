<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Event_manage
 * @author     jainik <jainik@raindropsinfotech.com>
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
class Event_manageViewDeclaration extends JViewLegacy
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
		$this->useremail = $user->get( 'email' );
		$step = JRequest::getVar('step');
		// $this->sid = JRequest::getVar('sid');
		$status = JRequest::getVar('status');
		$jinput = JFactory::getApplication()->input;
		if(!empty($user_id)){
			$this->sid = JRequest::getVar('sid');
		}else{
			$this->sid = '';
		}
		
		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->configemail = $componentParams->get('email');
		
		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');

        $formname = array('declaration');
        $this->form = $formModel->getForm($formname);
        $this->pageArr = array('event'=>'1','quotation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','checkout'=>'6');
        $this->pageKey = array('page1','page2','page3','page4','page5','page6','page7');
		if(!empty($this->sid)){
			$sid = '&sid='.$this->sid;
		}
		if($user_id){
			if(isset($this->sid)){
				$this->userdata = $eventModel->getSessionDataById($this->sid);
			}else{
				$this->userdata = $eventModel->getUserDataById($user_id);
			}

			/*$this->userdata = $eventModel->getUserDataById($user_id);*/
			$this->progress = $eventModel->getSessionDataById( $this->userdata->id );
			
			//if($session->get('event_id') == 'NULL'){
				$session->set('event_id', $this->userdata->id);
			//}
			
		}else{
			$this->progress = $eventModel->getSessionDataById($session->get('event_id'));
		}

		$this->PageOnedata = '';
		if($session->get('event_id', 'NULL') != 'NULL'){
			$this->PageOnedata = $session_model->getItem($session->get('event_id'));						
			$this->PageOnedata->data = unserialize($this->PageOnedata->data);				
		}
		
			$formdata = JRequest::getVar('jform');

			$this->form->bind($formdata);
		
		$this->newuserid = '';

		
			$this->errors = '';
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {			
			
			if($this->form->validate($formdata)){	
					
				$data['onpage']= '5';
				$data['create_date']= date('d-m-Y');
				// $data['data']['page1'] = $this->PageOnedata->data['page1'];
				// $data['data']['page2'] = $this->PageOnedata->data['page2'];
				// $data['data']['page3'] = $this->PageOnedata->data['page3'];	
				// $data['data']['page4'] = $this->PageOnedata->data['page4'];
				$data['data']['page5']['termconditions'] = $formdata['termconditions'];
				$data['data']['page5']['fsgread'] = $formdata['fsgread'];
				foreach ($this->pageKey as $value) {
				 	if($value != 'page5'){
						if(array_key_exists($value, $this->PageOnedata->data)){
							$data['data'][$value] = $this->PageOnedata->data[$value];
							$data['onpage'] = $this->PageOnedata->onpage;		
						}
					}
				}
		    	$data['status'] = '1';
		    	$data['activity_status'] = 'Unfinished';	
		    	
		    	if(empty($this->sid)){
		    		$this->sid = $formdata['sid'];
		    	}

		    	/*$data['Uid'] = $formdata['userid'];
		    	$data['Eid'] = $session->get('event_id');
		    	
		    	$ee = $eventModel->setEventsAndTerms($data);	    	

				if($ee === 'update'){
					JFactory::getApplication()->enqueueMessage('Page two store succesfully');	
					$url = JRoute::_('index.php?option=com_event_manage&view=event');
					$session->destroy(); 
					$app->redirect($url);
				}*/
				if($formdata['status'] == 'found'){
					$this->usercreate = $eventModel->getUserDetailByEmail($this->useremail);
					$this->newuserid = $this->usercreate->id;
					$data['Uid'] = $this->usercreate->id;
		    			$data['Eid'] = $session->get('event_id');
		    			if(!empty($this->sid)){
							$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
						}else{
							$ee = $eventModel->setEventsAndTerms($data);	    	
						}
		    			/*$ee = $eventModel->setEventsAndTerms($data);*/
		    			if($ee === 'update'){
		    				$body  = "<div>Detail about your policy: </div></br>";
					    	$body .= "<div>Your policy premium detail:<table border='1'>";
							$body .= "<tr>";
							$body .= "<th align='left'>Order No:</th>";
							$body .= "<td>". $session->get('event_id') ."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Insurence Name:</th>";
							$body .= "<td>".$data['data']['page1']['insurencename']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Start Date:</th>";
							$body .= "<td>".$data['data']['page1']['start_date']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Email:</th>";
							$body .= "<td>".$data['data']['page1']['email']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Public Liability Cover:</th>";
							$body .= "<td>$".$data['data']['page1']['liailitycover']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Premium:</th>";
							$body .= "<td>$".$data['data']['page2']['premium']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Broker Fee:</th>";
							$body .= "<td>$".$data['data']['page2']['brokerfee']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>GST Premium:</th>";
							$body .= "<td>$".$data['data']['page2']['gstpremium']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Stamp Duty:</th>";
							$body .= "<td>$".$data['data']['page2']['stampduty']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>GST Fee:</th>";
							$body .= "<td>$".$data['data']['page2']['gstfee']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Sub Total:</th>";
							$body .= "<td>$".$data['data']['page2']['subtotal']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Total Premium:</th>";
							$body .= "<td>$".$data['data']['page2']['totalpremium']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Commission:</th>";
							$body .= "<td>$".$data['data']['page2']['commission']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>GST Commission:</th>";
							$body .= "<td>$".$data['data']['page2']['gstcommission']."</td>";
							$body .= "</tr>";
							$body .= "</table></div><br>";
							$body .= "<div>Your policy address detail:<table border='1'>";
							$body .= "<tr>";
							$body .= "<th align='left'>FirstName:</th>";
							$body .= "<td>".$data['data']['page4']['firstname']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>LastName:</th>";
							$body .= "<td>".$data['data']['page4']['lastname']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Address:</th>";
							$body .= "<td>".$data['data']['page4']['streetaddress1']." ".$data['data']['page4']['streetaddress2']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Suburb:</th>";
							$body .= "<td>".$data['data']['page4']['suburb']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>State/Territory:</th>";
							$this->statename = $eventModel->getStateName();
							foreach ($this->statename as $key => $value) {
								if($value->id == $data['data']['page4']['state']){
									$statename = $value->name;
								}	
							}
							$body .= "<td>".$statename."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Postcode:</th>";
							$body .= "<td>".$data['data']['page4']['postcode']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Phone No:</th>";
							$body .= "<td>".$data['data']['page4']['phone']."</td>";
							$body .= "</tr>";
							$body .= "</table></div><br>";
							$body .= '<div>Thank & Regards</div><br>';
							$body .= '<div>Action Entertainment Insurance</div><br>';
							
					    	$mailData = array();
							$mailData['subject'] = "Test mail by action insurance";
							$mailData['sender'] = $data['data']['page1']['email'];
							if($this->configemail){
								$mailData['recipient'] = $this->configemail;
							}else{
								$mailData['recipient'] = $recipient;	
							}
							$mailData['body'] = $body;
							$send = $this->_sendMail($mailData, TRUE);

						$url = JRoute::_('index.php?option=com_event_manage&view=event');
						$session->destroy(); 
						$app->redirect($url);
					}	
				}
				
				/*$ee = $eventModel->setEventsAndTerms($data);*/
				if(!empty($this->sid)){
					$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
					$sid = '&sid='.$this->sid;
				}else{
					$ee = $eventModel->setEventsAndTerms($data);	    	
				}	    	
				if($ee != false ){
					JFactory::getApplication()->enqueueMessage('Page fifth store succesfully');	
					$url = JRoute::_('index.php?option=com_event_manage&view=checkout&step=6'.$sid);
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
		if($session->get('event_id', 'NULL') != 'NULL'){
			$this->frontdata = $session_model->getItem($session->get('event_id'));						
			$this->frontdata->data = unserialize($this->frontdata->data);	
			if($user_id != $this->frontdata->user){
				$session->get('event_id',NULL);
				$data = NULL;
			}			
		}
		
		if($this->frontdata != '' && isset($this->frontdata->data['page4'])){			
			$this->Pagefifthfrontdata = $this->frontdata->data['page5'];
			$this->form->bind($this->Pagefifthfrontdata);
		}else{
			
			if($session->get('event_id')){
				JFactory::getApplication()->enqueueMessage('Select this form first');	
			}
			if(!$user_id){
				$callback = '';
			}else{
				$callback = '&step=4'.$sid;
			}
			$url = JRoute::_('index.php?option=com_event_manage&view=address'.$callback);
			$app->redirect($url);
		}

		if (!$user_id) {
			JFactory::getApplication()->enqueueMessage('Please login first then move ahead');
			$url = JRoute::_('index.php?option=com_event_manage&amp;view=address&amp;step=4#openModalEmail');
			$app->redirect($url);	
		}

		if($user_id){
			/*$this->frontdata = $eventModel->getSessionDataById($user_id);*/
			$this->frontdatadata = unserialize($this->progress->data);	
			$this->frontdatadata = (Array) $this->frontdatadata['page5'];
			if(!isset($step)){
				foreach ($this->pageArr as $key => $value){
					if($value == $this->progress->onpage){
						$step = $value; 
						$url = JRoute::_('index.php?option=com_event_manage&view='.$key.'&step='.$step);
						$app->redirect($url);
					}
				}
			}

			$this->form->bind($this->frontdatadata);
		}

		parent::display($tpl);		
	}

	// public function _sendMail($mailData, $isHtml = FALSE){
			
	// 	$recipient = $mailData['recipient'];
	// 	$body = $mailData['body'];
	// 	$subject = $mailData['subject'];
	// 	$config = JFactory::getConfig();
	// 	$sender = array( 
	// 	    $config->get( 'mailfrom' ),
	// 	    $config->get( 'fromname' ) 
	// 	);
	// 	$mailer = JFactory::getMailer();
	
	// 	if($this->configemail){
	// 		$sender = array($this->configemail,$config->get( 'fromname' ));	
	// 	}
	// 	$attachment = $mailData['attachment'];
	// 	$mailer->setSender($sender);
	// 	$mailer->addRecipient($recipient);
	// 	$mailer->setSubject($subject);
	// 	$mailer->addAttachment($attachment);
	// 	$mailer->isHTML($isHtml);
	// 	$mailer->setBody($body);
	// 	return $mailer->Send();
	// }

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