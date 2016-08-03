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
class Event_manageViewCheckout extends JViewLegacy
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
		$status = JRequest::getVar('status');
		$jinput = JFactory::getApplication()->input;
		
		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');

        $formname = array('checkout');
        $this->form = $formModel->getForm($formname);
        $this->pageArr = array('event'=>'1','quoatation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','checkout'=>'6');
		
		if($user_id){
			$this->userdata = $eventModel->getUserDataById($user_id);
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
			// echo "<pre>"; print_r($_REQUEST); print_r($this->PageOnedata->data);
			// echo JRequest::getVar('submit_need_assistance'); exit;
			$event_id = $this->userdata->id;
			$user_id = $this->userdata->user;
			if($this->form->validate($formdata)){	
				// SECURE PAY CODE [NIKHIL]...
				$payment_success = FALSE;
				
				
					if($formdata['creditcard'] && JRequest::getVar('submit')){
						$error_message = "";

						include('components/com_event_manage/securepay.php');
						$merchat_id = "ABC0001"; // get from backend...
						$password = "abc123"; // get from backend...

						$sp = new SecurePay($merchat_id, $password);
						$sp->TestMode(); // Remove this line to actually preform a transaction - get from backend...
						$sp->TestConnection(); // check for connection...

						// for test only...
							// $sp->Cc = 4444333322221111;
							// $sp->ExpiryDate = '09/16';
							$sp->ChargeAmount = 108.08;
							// $sp->Cvv = 000;

						// for live...
							$sp->Cc = $formdata['cardnumber'];
							// $sp->ExpiryDate = $formdata['expirationdate'];
							$sp->ExpiryDate = $formdata['month']."/".$formdata['year'];
							// $sp->ChargeAmount = $formdata['orderTotal'];
							$sp->Cvv = $formdata['CVV'];

						$sp->ChargeCurrency = 'AUD'; // get from backend...
						$sp->OrderId = 'sp_'.$user_id.'_'.$event_id;

						// for recurring transaction...
						$sp->SetupRepeat('monthly', 6); // get from backend...

						if ($sp->Valid()) { // Is the above data valid?
							$response = $sp->Process();
							$payment_data = array();
							$payment_data['statusCode'] = (string)$response->Status->statusCode;
							$payment_data['statusDescription'] = (string)$response->Status->statusDescription;
							// for one time payment...
							// if((string)$response->Payment->TxnList->Txn->responseCode && strtoupper((String)$response->Payment->TxnList->Txn->approved) == "YES"){
							if($response->Payment){
								$payment_data['txnType'] = (string)$response->Payment->TxnList->Txn->txnType;
								$payment_data['txnSource'] = (string)$response->Payment->TxnList->Txn->txnSource;
								$payment_data['amount'] = (string)$response->Payment->TxnList->Txn->amount;
								$payment_data['currency'] = (string)$response->Payment->TxnList->Txn->currency;
								$payment_data['purchaseOrderNo'] = (string)$response->Payment->TxnList->Txn->purchaseOrderNo;
								$payment_data['approved'] = (string)$response->Payment->TxnList->Txn->approved;
								$payment_data['responseCode'] = (string)$response->Payment->TxnList->Txn->responseCode;
								$payment_data['responseText'] = (string)$response->Payment->TxnList->Txn->responseText;
								$payment_data['thinlinkResponseCode'] = (string)$response->Payment->TxnList->Txn->thinlinkResponseCode;
								$payment_data['thinlinkResponseText'] = (string)$response->Payment->TxnList->Txn->thinlinkResponseText;
								$payment_data['thinlinkEventStatusCode'] = (string)$response->Payment->TxnList->Txn->thinlinkEventStatusCode;
								$payment_data['thinlinkEventStatusText'] = (string)$response->Payment->TxnList->Txn->thinlinkEventStatusText;
								$payment_data['settlementDate'] = (string)$response->Payment->TxnList->Txn->settlementDate;
								$payment_data['txnID'] = (string)$response->Payment->TxnList->Txn->txnID;
								$payment_data['recurring'] = "No";
								if($response->Payment->TxnList->Txn->responseCode && (String)$response->Payment->TxnList->Txn->approved == "Yes"){
									$message = "Transaction was success!<br/>";
									$message .= "Code: ".$response->Payment->TxnList->Txn->responseCode."<br/>";
									$message .= "Text: ".$response->Payment->TxnList->Txn->responseText."<br/>";
									$message .= "Bank Trans ID: ".$response->Payment->TxnList->Txn->txnID."<br/>";
									$message .= "Settlement Date: ".substr($response->Payment->TxnList->Txn->settlementDate, 0, 4)."/".substr($response->Payment->TxnList->Txn->settlementDate, 4, 2)."/".substr($response->Payment->TxnList->Txn->settlementDate, 6, 2)."<br/>";
									$message .= "Approved: ".$response->Payment->TxnList->Txn->approved."<br/>";
									$message .= "Amount: ".((float)$response->Payment->TxnList->Txn->amount/100)." ".$response->Payment->TxnList->Txn->currency."<br/>";
									JFactory::getApplication()->enqueueMessage($message);
									$payment_success = TRUE;
								} else {
									$error_message = "Transaction failed with the error:<br/>";
									$error_message .= "Code: ".$response->Payment->TxnList->Txn->responseCode."<br/>";
									$error_message .= "Text: ".$response->Payment->TxnList->Txn->responseText."<br/>";
									JFactory::getApplication()->enqueueMessage($error_message);
								}
							// for recurring payment...
							// } else if((string)$response->Periodic->PeriodicList->PeriodicItem->responseCode && strtoupper((String)$response->Periodic->PeriodicList->PeriodicItem->successful) == "YES") {
							} else if($response->Periodic){
								$payment_data['actionType'] = (string)$response->Periodic->PeriodicList->PeriodicItem->actionType;
								$payment_data['clientID'] = (string)$response->Periodic->PeriodicList->PeriodicItem->clientID;
								$payment_data['responseCode'] = (string)$response->Periodic->PeriodicList->PeriodicItem->responseCode;
								$payment_data['responseText'] = (string)$response->Periodic->PeriodicList->PeriodicItem->responseText;
								$payment_data['successful'] = (string)$response->Periodic->PeriodicList->PeriodicItem->successful;
								$payment_data['txnType'] = (string)$response->Periodic->PeriodicList->PeriodicItem->txnType;
								$payment_data['amount'] = (string)$response->Periodic->PeriodicList->PeriodicItem->amount;
								$payment_data['currency'] = (string)$response->Periodic->PeriodicList->PeriodicItem->currency;
								$payment_data['txnID'] = (string)$response->Periodic->PeriodicList->PeriodicItem->txnID;
								$payment_data['receipt'] = (string)$response->Periodic->PeriodicList->PeriodicItem->receipt;
								$payment_data['ponum'] = (string)$response->Periodic->PeriodicList->PeriodicItem->ponum;
								$payment_data['settlementDate'] = (string)$response->Periodic->PeriodicList->PeriodicItem->settlementDate;
								if($response->Periodic->PeriodicList->PeriodicItem->responseCode && (String)$response->Periodic->PeriodicList->PeriodicItem->successful == "yes"){
									$message = "Transaction was success!";
									$message .= "Code: ".$response->Periodic->PeriodicList->PeriodicItem->responseCode."<br/>";
									$message .= "Text: ".$response->Periodic->PeriodicList->PeriodicItem->responseText."<br/>";
									$message .= "Bank Trans ID: ".$response->Periodic->PeriodicList->PeriodicItem->txnID."<br/>";
									$message .= "Settlement Date: ".substr($response->Periodic->PeriodicList->PeriodicItem->settlementDate, 0, 4)."/".substr($response->Periodic->PeriodicList->PeriodicItem->settlementDate, 4, 2)."/".substr($response->Periodic->PeriodicList->PeriodicItem->settlementDate, 6, 2)."<br/>";
									$message .= "Approved: ".$response->Periodic->PeriodicList->PeriodicItem->successful."<br/>";
									$message .= "Amount: ".((float)$response->Periodic->PeriodicList->PeriodicItem->amount/100)." ".$response->Periodic->PeriodicList->PeriodicItem->currency."<br/>";
									JFactory::getApplication()->enqueueMessage($message);
									$payment_success = TRUE;
								} else {
									$error_message = "Transaction failed with the error:<br/>";
									$error_message .= "Code: ".$response->Periodic->PeriodicList->PeriodicItem->responseCode."<br/>";
									$error_message .= "Text: ".$response->Periodic->PeriodicList->PeriodicItem->responseText."<br/>";
									JFactory::getApplication()->enqueueMessage($error_message);
								}
							} else {
								$error_message = "Transaction failed with the error:<br/>";
								$error_message .= "Code: ".$response->Status->statusCode."<br/>";
								$error_message .= "Text: ".$response->Status->statusDescription."<br/>";
								JFactory::getApplication()->enqueueMessage($error_message);
							}
						} else {
							$error_message = "Your cradit data is Invalid<br/>";
							if(!$sp->ValidCC()){
								$error_message .= "Error In CC";
							} else if(!$sp->ValidExpiryDate()){
								$error_message .= "Error In ExpiryDate";
							} else if(!$sp->ValidCvv()){
								$error_message .= "Error In Cvv";
							} else if(!$sp->ValidChargeAmount()){
								$error_message .= "Error In ChargeAmount";
							} else if(!$sp->ValidChargeCurrency()){
								$error_message .= "Error In ChargeCurrency";
							} else if(!$sp->ValidOrderId()){
								$error_message .= "Error In OrderId";
							}
							JFactory::getApplication()->enqueueMessage($error_message);
						}

						// Secure pay response variables...
				    	$data['sp_statusCode'] = $payment_data['statusCode'];
				    	$data['sp_statusDescription'] = $payment_data['statusDescription'];
				    	$data['sp_txnSource'] = $payment_data['txnSource'];
				    	$data['sp_purchaseOrderNo'] = $payment_data['purchaseOrderNo'];
				    	$data['sp_approved'] = $payment_data['approved'];
				    	$data['sp_thinlinkResponseCode'] = $payment_data['thinlinkResponseCode'];
				    	$data['sp_thinlinkResponseText'] = $payment_data['thinlinkResponseText'];
				    	$data['sp_thinlinkEventStatusCode'] = $payment_data['thinlinkEventStatusCode'];
				    	$data['sp_thinlinkEventStatusText'] = $payment_data['thinlinkEventStatusText'];
				    	$data['sp_recurring'] = $payment_data['recurring'];
				    	$data['sp_actionType'] = $payment_data['actionType'];
				    	$data['sp_clientID'] = $payment_data['clientID'];
				    	$data['sp_responseCode'] = $payment_data['responseCode'];
				    	$data['sp_responseText'] = $payment_data['responseText'];
				    	$data['sp_successful'] = $payment_data['successful'];
				    	$data['sp_txnType'] = $payment_data['txnType'];
				    	$data['sp_amount'] = $payment_data['amount'];
				    	$data['sp_currency'] = $payment_data['currency'];
				    	$data['sp_txnID'] = $payment_data['txnID'];
				    	$data['sp_receipt'] = $payment_data['receipt'];
				    	$data['sp_ponum'] = $payment_data['ponum'];
				    	$data['sp_settlementDate'] = $payment_data['settlementDate'];
				    	$data['sp_orderid'] = 'sp_'.$user_id.'_'.$event_id;

				    	if($payment_success){
					    	$data['activity_status'] = 'Active';
					    } else {
							$data['activity_status'] = 'Unpaid';
					    }
					} else if(JRequest::getVar('submit_need_assistance')){
						// send mail and status = HOLD...
						$data['activity_status'] = "Hold";
						$payment_success = TRUE;

						$body = "";
						$body .= "<div>Hello Test,</div><br/>";
						$body .= "<div><table border='1'>";
						$body .= "<tr>";
						$body .= "<th align='left'>Insurence Name:</th>";
						$body .= "<td>".$this->PageOnedata->data['page1']['insurencename']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Start Date:</th>";
						$body .= "<td>".$this->PageOnedata->data['page1']['start_date']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Email:</th>";
						$body .= "<td>".$this->PageOnedata->data['page1']['email']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Public Liability Cover:</th>";
						$body .= "<td>".$this->PageOnedata->data['page1']['liailitycover']."</td>";
						$body .= "</tr>";
						$body .= "</table></div><br/>";

						$body .= "<div><table border='1'>";
						$body .= "<tr>";
						$body .= "<th align='left'>Premium:</th>";
						$body .= "<td>".$this->PageOnedata->data['page2']['premium']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Broker Fee:</th>";
						$body .= "<td>".$this->PageOnedata->data['page2']['brokerfee']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Sub Total:</th>";
						$body .= "<td>".$this->PageOnedata->data['page2']['subtotal']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>GST Premium:</th>";
						$body .= "<td>".$this->PageOnedata->data['page2']['gstpremium']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Stamp Duty:</th>";
						$body .= "<td>".$this->PageOnedata->data['page2']['stampduty']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>GST Fee:</th>";
						$body .= "<td>".$this->PageOnedata->data['page2']['gstfee']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Total Premium:</th>";
						$body .= "<th>".$this->PageOnedata->data['page2']['totalpremium']."</th>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Commission:</th>";
						$body .= "<td>".$this->PageOnedata->data['page2']['commission']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>GST Commission:</th>";
						$body .= "<td>".$this->PageOnedata->data['page2']['gstcommission']."</td>";
						$body .= "</tr>";
						$body .= "</table></div>";

						$mailData = array();
						$mailData['subject'] = "Test mail - Insurance - I need Assisantce";
						$mailData['sender'] = "test.raindropsinfotech@gmail.com";
						$mailData['recipient'] = "test.raindropsinfotech@gmail.com";
						$mailData['body'] = $body;
						$this->_sendMail($mailData, TRUE);
					}
				
				// SECURE PAY CODE [NIKHIL]...	
				
				$data['create_date']= date('d-m-Y');
				$data['data']['page1'] = $this->PageOnedata->data['page1'];	
				$data['data']['page2'] = $this->PageOnedata->data['page2'];
				$data['data']['page3'] = $this->PageOnedata->data['page3'];
				$data['data']['page4'] = $this->PageOnedata->data['page4'];
				$data['data']['page5'] = $this->PageOnedata->data['page5'];
				$data['onpage']= '5';
				if($payment_success){
					$data['onpage']= '6';
					$data['data']['page6']['creditcard'] = $formdata['creditcard'];
					$data['data']['page6']['startdate'] = $formdata['startdate'];
					$data['data']['page6']['publicliailitycover'] = $formdata['publicliailitycover'];
					$data['data']['page6']['totalpremium'] = $formdata['totalpremium'];
					$data['data']['page6']['subtotal'] = $formdata['subtotal'];
					$data['data']['page6']['orderTotal'] = $formdata['orderTotal'];
					$data['data']['page6']['creditcardfee'] = $formdata['creditcardfee'];
					$data['data']['page6']['extrachreges'] = $formdata['extrachreges'];
			    	$data['status'] = '1';
			    }
		    	
		    	if($formdata['status'] == 'found'){
		    		$data['onpage']= '6';
		    		$data['status'] = '1';
					$this->usercreate = $eventModel->getUserDetailByEmail($this->useremail);
					$this->newuserid = $this->usercreate->id;
						$data['Uid'] = $this->usercreate->id;
		    			$data['Eid'] = $session->get('event_id');
		    			$ee = $eventModel->setEventsAndTerms($data);
		    			if($ee === 'update'){
							$url = JRoute::_('index.php?option=com_event_manage&view=event');
							$session->destroy(); 
							$app->redirect($url);
						}	
				}

			    $ee = $eventModel->setEventsAndTerms($data);	    	
				if($ee != false && $payment_success){
					JFactory::getApplication()->enqueueMessage('Page six store succesfully');	
					$url = JRoute::_('index.php?option=com_event_manage&view=Confirmation&step=7');
					$app->redirect($url);
				} else {
					$url = JRoute::_('index.php?option=com_event_manage&view=checkout&step=6');
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

		if($this->frontdata != '' && isset($this->frontdata->data['page5'])){			
			$this->event  = (Array)$eventModel->getEventById($this->frontdata->data['page1']['event']);
			$this->state 		= $eventModel->getFindState($this->frontdata->data['page1']['postcode']);		
			$this->premium = $eventModel->getPremium($this->frontdata->data['page2']['perfomers'],$this->state);
			$this->Pagesixfrontdata = $this->frontdata->data['page6'];
			$this->form->bind($this->Pagesixfrontdata);
		}else{
			JFactory::getApplication()->enqueueMessage('Select this form first');	
			$url = JRoute::_('index.php?option=com_event_manage&view=declaration&step=5');
			$app->redirect($url);
		}

/*		if (!$user_id) {
			JFactory::getApplication()->enqueueMessage('Please login first then move ahead');
			$url = JRoute::_('index.php?option=com_event_manage&amp;view=address&amp;step=4#openModalEmail');
			$app->redirect($url);	
		}*/

		if($user_id){
			/*$this->frontdata = $eventModel->getSessionDataById($user_id);*/
			$this->frontdatadata = unserialize($this->progress->data);	
			$this->frontdatadata = (Array) $this->frontdatadata['page6'];
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
		
		$this->Extracharegs = $eventModel->getTerm('Excharge');
		$this->CreditCardFee = $eventModel->getTerm('CCF');
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