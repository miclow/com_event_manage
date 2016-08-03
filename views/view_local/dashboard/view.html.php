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
class Event_manageViewDashboard extends JViewLegacy
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
		$this->status = JRequest::getVar('status');
		$this->oid = JRequest::getVar('oid');
		$thsi->groups = JUserHelper::getUserGroups($user_id);
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
		
		$this->errors = '';

		if(!empty($this->status) && in_array($this->status, array('Y','N')) && !empty($this->oid)){
			$changestatus = $eventModel->changeOrderStatus($this->oid,$this->status);	
			if($changestatus != false){
					JFactory::getApplication()->enqueueMessage('order no '.$this->oid.' remove succesfully');
					$url = JRoute::_('index.php?option=com_event_manage&view=dashboard');	
					$app->redirect($url);
			}
		}


		if(!empty($this->status) && $this->status == 'cancel' && !empty($this->oid)){
				$policy_no = $eventModel->getPolicyNumberByOrderId($this->oid);
				$body = "Please cancel policy. My policy detail given below </br>";
				$body .= "Policy No. : ".$policy_no->policy_number."</br>";
				$body .= "Order No. : ".$this->oid."</br>";
				$mailData = array();
				$mailData['subject'] = "Cancel policy";
				$senderemail = $user->get('email');
				$mailData['sender'] = $senderemail;
				$mailData['body'] = $body;

				$send = $this->_sendMail($mailData, TRUE);
				
				if($send){
					if($policy_no->policy_cancel == 'N'){	
						$status = 'N';
						$policy_cancel = 'Y';
						$changestatus = $eventModel->changeOrderStatus($this->oid,$status,$policy_cancel);
					}
					$url = JRoute::_('index.php?option=com_event_manage&view=dashboard');	
					$app->redirect($url);
				}
		}

		$this->userdetail = $eventModel->getDataByIdForDashborad($user_id);
		/*print_r($this->userdetail);*/
		$this->gst = $eventModel->getTerm('GST');

		if(!$user_id){
			$url = JRoute::_('index.php?option=com_event_manage&view=event');
			$app->redirect($url);
		}

		parent::display($tpl);					
	}

	public function _sendMail($mailData, $isHtml = FALSE){
			
		$body = $mailData['body'];
		$subject = $mailData['subject'];
		$config = JFactory::getConfig();
		$recipient = $config->get( 'mailfrom' );
		//$senderemail = $user->get('email');
		$sender = $mailData['sender'];
		$mailer = JFactory::getMailer();
	
		// if($this->configemail){
		// 	$sender = array($this->configemail,$config->get( 'fromname' ));	
		// }

		if($this->configemail){
			$recipient = $this->configemail;	
		}

		// echo $recipient;
		// print_r($mailData);
		// die;
		$mailer->setSender($sender);
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->isHTML($isHtml);
		$mailer->setBody($body);
		return $mailer->Send();
	}

	public function _download($file){

		$filepath = 'components/pdf/'.$file;
		$path = JPATH_ROOT.'/'.$filepath;

		header("Cache-Control: public");
		header("Content-Disposition: attachment; filename=" . urlencode($file));   
		header("Content-Type: application/pdf");
		header("Content-Description: File Transfer"); 
		header("Content-Transfer-Encoding: binary");           
		header("Content-Length: " . filesize($path));
		//flush(); // this doesn't really matter.
		$fp = fopen($path, "r");
		while (!feof($fp))
		{
		    echo fread($fp, 65536);
		    flush(); // this is essential for large downloads
		} 
		
		fclose($fp); 
		exit;
	}


}