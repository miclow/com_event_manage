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
				$res = $eventModel->gernatePDF($userdata);
				$pathArr = explode('/', $res);
				$filename = $pathArr[count($pathArr)-1]; 
				$filenamesave = $eventModel->savepdffilename($pid,$filename);
				$data = unserialize($userdata->data);	
				if(!empty($data['page4']['email'])){
					$recipient = $data['page4']['email'];
				} else {
					$recipient = $data['page1']['email'];
				}

				
				$body = "<div>Check mail for unpaid to paid by manully</div><br/>";
				$body .= "<div><table border='1'>";
				$body .= "<tr>";
				$body .= "<th align='left'>Policy Number:</th>";
				$body .= "<td>".$userdata->policy_number."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Order No:</th>";
				$body .= "<td>".$userdata->id."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Insurence Name:</th>";
				$body .= "<td>".$data['page1']['insurencename']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Start Date:</th>";
				$body .= "<td>".$data['page1']['start_date']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Email:</th>";
				$body .= "<td>".$data['page1']['email']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Public Liability Cover:</th>";
				$body .= "<td>".$data['page1']['liailitycover']."</td>";
				$body .= "</tr>";
				$body .= "</table></div><br/>";
				$body .= "<div><table border='1'>";
				$body .= "<tr>";
				$body .= "<th align='left'>Premium:</th>";
				$body .= "<td>".$data['page2']['premium']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Broker Fee:</th>";
				$body .= "<td>".$data['page2']['brokerfee']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>GST Premium:</th>";
				$body .= "<td>".$data['page2']['gstpremium']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Stamp Duty:</th>";
				$body .= "<td>".$data['page2']['stampduty']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>GST Fee:</th>";
				$body .= "<td>".$data['page2']['gstfee']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Total Premium:</th>";
				$body .= "<th>".$data['page6']['totalpremium']."</th>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Sub Total:</th>";
				$body .= "<td>".$data['page6']['subtotal']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>Commission:</th>";
				$body .= "<td>".$data['page2']['commission']."</td>";
				$body .= "</tr>";
				$body .= "<tr>";
				$body .= "<th align='left'>GST Commission:</th>";
				$body .= "<td>".$data['page2']['gstcommission']."</td>";
				$body .= "</tr>";
				$body .= "</table></div><br>";
				$body .= '<div>Thank Rgards</div><br>';
				$body .= '<div>Action Entertainment Insurance</div><br>';

				$mailData = array();
				$mailData['subject'] = "Test mail for unpaid to paid by manully";
				$mailData['recipient'] = $recipient;
				$mailData['body'] = $body;
				$mailData['attachment'] = $res;
				$send = $this->_sendMail($mailData, TRUE);

				$url = JRoute::_('index.php?option=com_event_manage&view=userslist');
				$app->redirect($url);
			}
		}


		$formdata = JRequest::getVar('jform');
		$this->form->bind($formdata);
		$this->errors = '';
	
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!empty($formdata['orderid']) || !empty($this->orderid))){
			
			if($this->form->validate($formdata)){	
				
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
				if($ReffralPageArr['page1']['perfomers'] == '13+' && $ReffralPageArr['page1']['annualincome'] == '$250,000+'){
					$ExtraFieldExits = $eventModel->getNumberOfExtraFieldOrderWise($data['order_id'],$data['onpage'],$ReffralPageArr['page1']['perfomers']);
					if(!$ExtraFieldExits){
						$data['numberofreffral'] = 2;
					}
				}
				

				if($formdata['performers'] == '13+'){
					$data['premium'] = $formdata['premium'];
					$data['brokerfee'] = $formdata['brokerfee'];
					$data['activity'] = 'Excharge';	
					$pageone = array('premium','brokerfee');
				}
				// echo "<pre>";
				// print_r($data);
				// echo "</pre>";
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
					
					if($emaildata['page1']['perfomers'] == '13+'){
						$perfomers = '13+';
					}

					$this->extrpremium = $eventModel->getExtraFieldOrderWise($orderid);

					$body = "Check mail for extra field added Detail";
					$body .= '<div><table border="1">';
					$body .= '<tbody>';
					$body .= '<tr>';
					$body .= '<th align="left">';
					$body .= 'Name:';
					$body .= '</th>';
					$body .= '<th align="left">';
					$body .= 'Price/Value:';
					$body .= '</th>';
					$body .= '</tr>';
					foreach ($this->extrpremium as $evalue) {
						$body .= '<tr>';
						$body .= '<td>';
						$body .= $this->extrpremium[0]->name;
						$body .= '</td>';
						$body .= '<td>';
						$body .= $this->extrpremium[0]->price;
						$body .= '</td>';
						$body .= '</tr>';
					}
					$body .= '</tbody>';
					$body .= '</table></div><br/>';
					$body .= '<div>Thank Rgards</div><br/>';
					$body .= '<div>Action Entertainment Insurance</div>';

					$mailData = array();
					$mailData['subject'] = "Test mail for extra field added";
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
		
		// $perfomers = '13+';
		//$this->extrpremium = $eventModel->getExtraFieldOrderWise($session->get('event_id'),'13+');
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
		$mailer->setSender($sender);
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->addAttachment($attachment);
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
		header("Content-Length: " . filesize($path));
		flush(); // this doesn't really matter.
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