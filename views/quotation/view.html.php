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
class Event_manageViewQuotation extends JViewLegacy
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
		if(!empty($user_id)){
			$this->sid = JRequest::getVar('sid');
		}else{
			$this->sid = '';
		}
		$status = JRequest::getVar('status');
		$email = JRequest::getVar('email1');

		$jinput = JFactory::getApplication()->input;
		
		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->configemail = $componentParams->get('email');

		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');

        $formname = array('quotation');
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
				if($session->get('event_id', 'NULL') != 'NULL'){
					$this->userdata = $eventModel->getSessionDataById($session->get('event_id'));	
				}else{
					$this->userdata = $eventModel->getUserDataById($user_id);
				}
			}
			/*$this->userdata = $eventModel->getUserDataById($user_id);*/
			$this->progress = $eventModel->getSessionDataById( $this->userdata->id );
			$session->set('event_id', $this->userdata->id);
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
			if ($_SERVER['REQUEST_METHOD'] !== 'POST'){	
				if($status == 'notfound'){	
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
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){			
			/*$data['terms'] = $jinput->post->get('terms','');
			$data['event'] = $jinput->post->get('event','');	*/

			if($this->form->validate($formdata)){	
					
				$data['onpage']= '2';
				$data['create_date']= date('d-m-Y');
				// $data['data']['page1'] = $this->PageOnedata->data['page1'];	
				$data['data']['page2']['premium'] = $formdata['premium'];
				$data['data']['page2']['brokerfee'] = $formdata['brokerfee'];
				$data['data']['page2']['subtotal'] = $formdata['subtotal'];
				$data['data']['page2']['gstpremium'] = $formdata['gstpremium'];
				$data['data']['page2']['stampduty'] = $formdata['stampduty'];
				$data['data']['page2']['gstfee'] = $formdata['gstfee'];
				$data['data']['page2']['totalpremium'] = $formdata['totalpremium'];
				$data['data']['page2']['commission'] = $formdata['commission'];
				$data['data']['page2']['gstcommission'] = $formdata['gstcommission'];
				foreach ($this->pageKey as $value) {
				 	if($value != 'page2'){
				 		if($this->PageOnedata->onpage > $data['onpage']){
				 			$data['onpage'] = $this->PageOnedata->onpage;
				 		}
						if(array_key_exists($value, $this->PageOnedata->data)){
							$data['data'][$value] = $this->PageOnedata->data[$value];
						}
					}
				}
		    	$data['status'] = '1';
		    	$data['activity_status'] = 'Unfinished';	
				

			    $data['Uid'] = $formdata['userid'];
		    	$data['Eid'] = $session->get('event_id');
		    	
		    	/*$ee = $eventModel->setEventsAndTerms($data);*/	    	

		    	if(!empty($this->sid)){
					$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
					$sid = '&sid='.$this->sid;
				}else{
					$ee = $eventModel->setEventsAndTerms($data);	    	
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
						
					}
				     	
				    $session->clear('savecomebackemail');
					JFactory::getApplication()->enqueueMessage('Page two stored successfully');	
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
						$body .= "<td>". $session->get('event_id')."</td>";
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
						$body .= '<div>Thank & Regards</div><br>';
						$body .= '<div>Action Entertainment Insurance</div><br>';
						
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
					}
				    	
				    $session->clear('savecomebackemail');
					JFactory::getApplication()->enqueueMessage('Page two stored successfully');	
					$url = JRoute::_('index.php?option=com_event_manage&view=proposal&step=3'.$sid);
					$app->redirect($url);

				}
			}else{
				
				$this->errors = '<ul class="server_error">';
				
				foreach ($this->form->getErrors() as $key => $value) {
					$this->errors .= '<li>'.$value->getMessage().'</li>';
				}				
				$this->errors .= '</ul>';
			}

			/*$this->events = $eventModel->setEventsAndTerms($data);*/						
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
			/*echo $session->get('event_id');
			var_dump($this->frontdata->data); exit;*/

			if($this->frontdata != '' && isset($this->frontdata->data['page1'])){			
				if($this->frontdata->data['page1']['start_date']){
					$this->event  = $eventModel->getEventById($this->frontdata->data['page1']['event']);		
					$this->state =$eventModel->getFindState($this->frontdata->data['page1']['postcode']);
					
					if($this->frontdata->data['page1']['perfomers'] != '13+'){
						$this->premium = $eventModel->getPremium($this->frontdata->data['page1']['perfomers'],$this->state);
					}else{
						$perfomers = '13+';
						$this->extrpremium = $eventModel->getExtraFieldOrderWise($session->get('event_id'),$perfomers);
						// echo "<pre>";	
						// print_r($this->extrpremium);
						// echo "</pre>";
					}

					$this->stampduty = $eventModel->getTermByName($this->state);
					$this->Pagetwofrontdata = $this->frontdata->data['page2'];
					$this->form->bind($this->Pagetwofrontdata);
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
				if($session->get('event_id')){
					JFactory::getApplication()->enqueueMessage('Select this form first');	
				}
				if(!$user_id){
					$callback = '';
				}else{
					$callback = '&step=1'.$sid;
				}
				$url = JRoute::_('index.php?option=com_event_manage&view=event'.$callback);
				$app->redirect($url);
			}


		if($user_id){
			/*$this->frontdata = $eventModel->getSessionDataById($user_id);*/
			$this->frontdatadata = unserialize($this->progress->data);	
			$this->frontdatadata = (Array) $this->frontdatadata['page2'];
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
		$this->perfomers = $eventModel->getTerm('Perfomers');
		$this->gst = $eventModel->getTerm('GST');
		$this->commission = $eventModel->getTerm('Commission');
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