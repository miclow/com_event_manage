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
class Event_manageViewAddress extends JViewLegacy
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
		$step = JRequest::getVar('step');
		if(!empty($user_id)){
			$this->sid = JRequest::getVar('sid');
		}else{
			$this->sid = '';
		}
		$status = JRequest::getVar('status');

		$jinput = JFactory::getApplication()->input;
		$config = JFactory::getConfig();
		$recipient = $config->get( 'mailfrom' );
		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->configemail = $componentParams->get('email');
		
		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');

        $formname = array('address');
        $this->form = $formModel->getForm($formname);
        $this->pageArr = array('event'=>'1','quotation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','checkout'=>'6');
        $this->pageKey = array('page1','page2','page3','page4','page5','page6','page7');
        if(!empty($this->sid)){
			$sid = '&sid='.$this->sid;
		}
        // echo $session->get('event_id').'<<<<<<<';
        if($user_id){
        	
        	if(isset($this->sid)){
				$this->userdata = $eventModel->getSessionDataById($this->sid);
			}else{
				if($session->get('event_id', 'NULL') != 'NULL'){
					$this->userdata = $eventModel->getSessionDataById($session->get('event_id'));	
				}else{
					$this->userdata = $eventModel->getUserDataById($user_id);
				}
			}
			
			/*$this->userdata = $eventModel->getUserDataById($user_id);*/
			$this->progress = $eventModel->getSessionDataById( $this->userdata->id );
			/*if($session->get('event_id') == 'NULL'){*/
				$session->set('event_id', $this->userdata->id);
			/*}*/
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

			$this->errors = '';
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {			
			
			if($this->form->validate($formdata)){	
					
				$data['onpage']= '4';
				$data['create_date']= date('d-m-Y');
				// $data['data']['page1'] = $this->PageOnedata->data['page1'];
				// $data['data']['page2'] = $this->PageOnedata->data['page2'];
				// $data['data']['page3'] = $this->PageOnedata->data['page3'];	
				$data['data']['page4']['firstname'] = $formdata['firstname'];
				$data['data']['page4']['lastname'] 	= $formdata['lastname'];
				$data['data']['page4']['insuredname'] = $formdata['insuredname'];
				$data['data']['page4']['streetaddress1'] = $formdata['streetaddress1'];
				$data['data']['page4']['streetaddress2'] = $formdata['streetaddress2'];
				$data['data']['page4']['suburb'] = $formdata['suburb'];
				$data['data']['page4']['state'] = $formdata['state'];
				$data['data']['page4']['postcode'] = $formdata['postcode'];
				$data['data']['page4']['phone'] = $formdata['phone'];
				$data['data']['page4']['sameaddress'] = $formdata['sameaddress'];
				if($formdata['sameaddress'][0] == 'Y'){
					$data['data']['page4']['billingaddress1'] = $formdata['streetaddress1'];
					$data['data']['page4']['billingaddress2'] = $formdata['streetaddress2'];
					$data['data']['page4']['billingsuburb'] = $formdata['suburb'];
					$data['data']['page4']['billingstate'] = $formdata['state'];
					$data['data']['page4']['billingpostcode'] = $formdata['postcode'];
				}else{
					$data['data']['page4']['billingaddress1'] = $formdata['billingaddress1'];
					$data['data']['page4']['billingaddress2'] = $formdata['billingaddress2'];
					$data['data']['page4']['billingsuburb'] = $formdata['billingsuburb'];
					$data['data']['page4']['billingstate'] = $formdata['billingstate'];
					$data['data']['page4']['billingpostcode'] = $formdata['billingpostcode'];
				}
				$data['data']['page4']['email'] = $formdata['confirmemail'];
				// $data['data']['page4']['confirmemail'] = $formdata['confirmemail'];
				foreach ($this->pageKey as $value) {
					if($this->PageOnedata->onpage > $data['onpage']){
				 			$data['onpage'] = $this->PageOnedata->onpage;
				 		}
				 	if($value != 'page4'){
						if(array_key_exists($value, $this->PageOnedata->data)){
							$data['data'][$value] = $this->PageOnedata->data[$value];
						}
					}
				}
				$data['data']['page1']['insurencename'] = $formdata['insuredname'];
				$data['data']['page1']['postcode'] = $formdata['postcode'];
		    	$data['status'] = '1';	
				$data['activity_status'] = 'Unfinished';
				if(empty($this->sid)){
					$this->sid = $formdata['sid'];
				}
		    		/*$ee = $eventModel->setEventsAndTerms($data);	    	
				}
				if($ee != false ){
					JFactory::getApplication()->enqueueMessage('Page four stored successfully');	
					$url = JRoute::_('index.php?option=com_event_manage&view=declaration&step=5');
					$app->redirect($url);
				}*/

				if(empty($formdata['insuredname'])){
		    			$data['data']['page4']['insuredname'] = $this->PageOnedata->data['page1']['insurencename'];
		    		}
		    		
				if($formdata['status'] == 'notfound') {
					
					$this->PageOnedata->data['page1'] = $data['data']['page4'];
					$this->usercreate = $eventModel->setCreateNewUser($this->PageOnedata->data);
					$data['Uid'] = $this->usercreate[2];
		    			$data['Eid'] = $session->get('event_id');
		    			/*$ee = $eventModel->setEventsAndTerms($data);*/
		    			if(!empty($this->sid)){
							$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
							$sid = '&sid='.$this->sid;
						}else{
							$ee = $eventModel->setEventsAndTerms($data);	    	
						}
		    			if($ee === 'update'){
		    				$body  = "<div>Detail about your policy: </div></br></br>";
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
							$body .= '<div>Thank & Regards<br>';
							$body .= 'Action Entertainment Insurance</div><br>';
					    	$mailData = array();
							$mailData['subject'] = "save and come back later";
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

				if($formdata['status'] == 'found'){
					$this->usercreate = $eventModel->getUserDetailByEmail($data['data']['page4']['email']);
					$this->newuserid = $this->usercreate->id;
					$data['Uid'] = $this->usercreate->id;
		    			$data['Eid'] = $session->get('event_id');
		    			/*$ee = $eventModel->setEventsAndTerms($data);*/
		    			if(!empty($this->sid)){
							$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
							$sid = '&sid='.$this->sid;
						}else{
							$ee = $eventModel->setEventsAndTerms($data);	    	
						}
		    			if($ee === 'update'){
		    				$body  = "<div>Detail about your policy: </div></br></br>";
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
							$body .= '<div>Thank & Regards</br>';
							$body .= 'Action Entertainment Insurance</div><br>';
							
					    	$mailData = array();
							$mailData['subject'] = "save and come back later";
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
				
				$this->usercreate = $eventModel->getUserDetailByEmail($data['data']['page4']['email']);
				if(!$this->usercreate){
					$this->PageOnedata->data['page1'] = $data['data']['page4'];
					$this->usercreate = $eventModel->setCreateNewUser($this->PageOnedata->data);
					$credentials = array(); 
					$credentials['username']  =  $this->usercreate[0];
					$credentials['password']  = $this->usercreate[1];
					$app->login($credentials);
					/*$ee = $eventModel->setEventsAndTerms($data);*/	    	
					if(!empty($this->sid)){
						$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
						$sid = '&sid='.$this->sid;
					}else{
						$ee = $eventModel->setEventsAndTerms($data);	    	
					}
					if($ee != false ){
						JFactory::getApplication()->enqueueMessage('Page four stored successfully');	
						$url = JRoute::_('index.php?option=com_event_manage&view=declaration&step=5'.$sid);
						$app->redirect($url);
					}
				}else{
					if(!$user_id){
						$session->set('fourfrontdata',$formdata);
						$url = JRoute::_('index.php?option=com_event_manage&view=address&step=4#openModalLogin');
						$app->redirect($url);
					}else{
						
						if(!empty($this->sid)){
							$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
							$sid = '&sid='.$this->sid;
						}else{
							$ee = $eventModel->setEventsAndTerms($data);	    	
						}
						if($ee != false ){
							JFactory::getApplication()->enqueueMessage('Page four stored successfully');	
							echo $url = JRoute::_('index.php?option=com_event_manage&view=declaration&step=5'.$sid);
							$app->redirect($url);
						}
					}
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
		
		if($status == 'notfound'){
			$this->PageOnedata->data['page1'] = $this->frontdata->data['page4'];
			$this->usercreate = $eventModel->setCreateNewUser($this->PageOnedata->data);
			$credentials = array(); 
			$credentials['username']  =  $this->usercreate[0];
			$credentials['password']  = $this->usercreate[1];
			$app->login($credentials);
			$app->redirect(JRoute::_('index.php?option=com_event_manage&view=declaration&step=5'));
		}
		
		if($this->frontdata != '' && isset($this->frontdata->data['page3'])){
			if($this->frontdata->data['page1']['start_date']){			
				$this->Pagefourfrontdata = $this->frontdata->data['page4'];
				$this->form->bind($this->Pagefourfrontdata);
			}else{
				JFactory::getApplication()->enqueueMessage('policy start date shuold not be blank','error');	
				if(!$user_id){
					$callback = '';
				}else{
					$callback = '&step=back'.$sid;
				}
				$url = JRoute::_('index.php?option=com_event_manage&view=event'.$callback);
				$app->redirect($url);
			}
		}else{
			JFactory::getApplication()->enqueueMessage('Select this form first');
			if(!$user_id){
				$callback = '';
			}else{
				$callback = '&step=3'.$sid;
			}	
			$url = JRoute::_('index.php?option=com_event_manage&view=proposal'.$callback);
			$app->redirect($url);
		}

		if($user_id){
			/*$this->frontdata = $eventModel->getSessionDataById($user_id);*/
			$this->Allfrontdata = unserialize($this->progress->data);	
			$this->frontdatapagefour = (Array) $this->Allfrontdata['page4'];
			
			if(!isset($step)){
				foreach ($this->pageArr as $key => $value){
					if($value == $this->progress->onpage){
						$step = $value; 
						$url = JRoute::_('index.php?option=com_event_manage&view='.$key.'&step='.$step);
						$app->redirect($url);
					}
				}
			}

			$fourfrontdata = $session->get( 'fourfrontdata' );
			if(empty($this->frontdatapagefour)){
				$this->form->bind($fourfrontdata);
			}else{
				$this->form->bind($this->frontdatapagefour);
			}
			$session->clear('fourfrontdata');
		}
		
		$this->statename = $eventModel->getStateName();
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