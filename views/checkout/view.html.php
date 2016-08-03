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
		// $this->sid = JRequest::getVar('sid');
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
		$this->merchatid = $componentParams->get('merchatid');
		$this->password = $componentParams->get('password');
		$this->configemail = $componentParams->get('email');
		$this->testmode = $componentParams->get('testmode');
		$this->chargecurrency = $componentParams->get('chargecurrency');
		$this->setuprepeat = $componentParams->get('setuprepeat');
		$this->interval = $componentParams->get('interval');
/*
		echo $this->merchatid.'<br>'; 
		echo $this->password.'<br>';
		echo $this->configemail.'<br>';
		echo $this->testmode.'<br>';
		echo $this->chargecurrency.'<br>';
		echo $this->setuprepeat.'<br>';
		echo $this->interval.'<br>';*/

		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');

        $formname = array('checkout');
        $this->form = $formModel->getForm($formname);
        $this->pageArr = array('event'=>'1','quotation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','checkout'=>'6');
		
		if($user_id){
			if(isset($this->sid)){
				$this->userdata = $eventModel->getSessionDataById($this->sid);
			}else{
				$this->userdata = $eventModel->getUserDataById($user_id);
			}
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
			if(!empty($this->sid)){
					$sid = '&sid='.$this->sid;
			}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {			
			// echo "<pre>"; print_r($_REQUEST); print_r($this->PageOnedata->data);
			// echo JRequest::getVar('submit_need_assistance'); exit;
			$event_id = $this->userdata->id;
			$user_id = $this->userdata->user;
			if($this->form->validate($formdata)){	
				// SECURE PAY CODE [NIKHIL]...
				$payment_success = FALSE;
				$hold_status = FALSE;
					if($formdata['creditcard'] && JRequest::getVar('submit')){
						$error_message = "";

						include('components/com_event_manage/securepay.php');
						// get from backend...
						if(!empty($this->merchatid)){
							$merchat_id = $this->merchatid;
						}else{
							$merchat_id = "ABC0001";
						}
						// get from backend...
						if(!empty($this->password)){
							$password = $this->password; 
						}else{
							$password = "abc123"; 
						}

						$sp = new SecurePay($merchat_id, $password);
						if($this->testmode){
							$sp->TestMode(); // Remove this line to actually preform a transaction - get from backend...
						}

						$sp->TestConnection(); // check for connection...

						// for test only...
							// $sp->Cc = 4444333322221111;
							// $sp->ExpiryDate = '09/16';
							// $sp->ChargeAmount = 108.08;
							// $sp->Cvv = 000;

						// for live...
							$sp->Cc = $formdata['cardnumber'];
							$sp->Cvv = $formdata['CVV'];
							$sp->ExpiryDate = $formdata['month']."/".$formdata['year'];
							// $sp->ChargeAmount = $formdata['orderTotal'];
							if($this->testmode){
								$sp->ChargeAmount = 108.08;
							}else{
								$sp->ChargeAmount = $formdata['orderTotal'];	
							}

						$sp->ChargeCurrency = $this->chargecurrency; // get from backend...
						$sp->OrderId = 'sp_'.$user_id.'_'.$event_id; // gernate policy number

						// for recurring transaction...
						if(!empty($this->setuprepeat) && !empty($this->interval)){
							$sp->SetupRepeat($this->setuprepeat, $this->interval); // get from backend...	
						}else{
							$sp->SetupRepeat('monthly', 6); // get from backend...	
						}
						

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
									$message = "Transaction was success!<br/>";
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
					    	$data['sp_status'] = 'Active';
					    } else {
							$data['activity_status'] = 'Unpaid';
							$data['sp_status'] = 'Unpaid';
					    }
					} else if(JRequest::getVar('submit_need_assistance')){
						// send mail and status = HOLD...
						$data['activity_status'] = "Hold";
						$hold_status = TRUE;

						$body = "";
						$body .= "<div>Hello ,</div><br/>";
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
						$body .= "<td>$".$this->PageOnedata->data['page2']['premium']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Broker Fee:</th>";
						$body .= "<td>$".$this->PageOnedata->data['page2']['brokerfee']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Sub Total:</th>";
						$body .= "<td>$".$this->PageOnedata->data['page2']['subtotal']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>GST Premium:</th>";
						$body .= "<td>$".$this->PageOnedata->data['page2']['gstpremium']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Stamp Duty:</th>";
						$body .= "<td>$".$this->PageOnedata->data['page2']['stampduty']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>GST Fee:</th>";
						$body .= "<td>$".$this->PageOnedata->data['page2']['gstfee']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Total Premium:</th>";
						$body .= "<th>$".$this->PageOnedata->data['page2']['totalpremium']."</th>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>Commission:</th>";
						$body .= "<td>$".$this->PageOnedata->data['page2']['commission']."</td>";
						$body .= "</tr>";
						$body .= "<tr>";
						$body .= "<th align='left'>GST Commission:</th>";
						$body .= "<td>$".$this->PageOnedata->data['page2']['gstcommission']."</td>";
						$body .= "</tr>";
						$body .= "</table></div>";
						$body .= '<div>Thank & Regards<br/>';
						$body .= 'Action Entertainment Insurance</div>';

						$mailData = array();
						$mailData['subject'] = "Test mail - Insurance - I need Assisantce";
						$mailData['sender'] = $this->PageOnedata->data['page1']['email'];
						if($this->configemail){
							$mailData['recipient'] = $this->configemail;	
						}else{
							$mailData['recipient'] = $recipient;	
						}
						
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
				if($hold_status){
					$data['onpage']= '6';
					$data['data']['page6']['creditcard'] = $formdata['creditcard'];
					$data['data']['page6']['startdate'] = $formdata['startdate'];
					$data['data']['page6']['publicliailitycover'] = $formdata['publicliailitycover'];
					$data['data']['page6']['totalpremium'] = $formdata['totalpremium'];
					$data['data']['page6']['subtotal'] = $formdata['subtotal'];
					$data['data']['page6']['orderTotal'] = $formdata['orderTotal'];
					$data['data']['page6']['creditcardfee'] = $formdata['creditcardfee'];
					$data['data']['page6']['extrachreges'] = $formdata['extrachreges'];
				}else{
					$data['onpage']= '5';
				}
				$data['status'] = '1';
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

			    	$pno = 1000+$session->get('event_id');
			    	$policyno = $eventModel->createPolicyNumber($pno);
			    	$data['policy_number'] = $policyno;
			    }
		    	
		    	if($formdata['status'] == 'found'){
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
					$this->usercreate = $eventModel->getUserDetailByEmail($this->useremail);
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
		    				$body = "";
							$body .= "<div>Detail about your policy : </div><br/>";
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
							$body .= "<th align='left'>Total Premium:</th>";
							$body .= "<th>$".$data['data']['page6']['totalpremium']."</th>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Sub Total:</th>";
							$body .= "<td>$".$data['data']['page6']['subtotal']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Order Total:</th>";
							$body .= "<td>$".$data['data']['page6']['orderTotal']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>Credit Card Fee:</th>";
							$body .= "<td>$".$data['data']['page6']['creditcardfee']."</td>";
							$body .= "</tr>";
							$body .= "<th align='left'>Commission:</th>";
							$body .= "<td>$".$data['data']['page2']['commission']."</td>";
							$body .= "</tr>";
							$body .= "<tr>";
							$body .= "<th align='left'>GST Commission:</th>";
							$body .= "<td>$".$data['data']['page2']['gstcommission']."</td>";
							$body .= "</tr>";
							$body .= "</table></div>";
							$body .= '<div>Thank & Regards</div></br>';
							$body .= '<div>Action Entertainment Insurance</div>';
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

			    /*$ee = $eventModel->setEventsAndTerms($data);*/	    	
			    if(!empty($this->sid)){
			    	$data['Uid'] = $user_id;
		    		$data['Eid'] = $session->get('event_id');
					$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
					$sid = '&sid='.$this->sid;
				}else{
					$ee = $eventModel->setEventsAndTerms($data);	    	
				}
				
				if($ee != false && $payment_success){

					$userdata = $eventModel->getSessionDataById($session->get('event_id'));
					/* Gernate Policy PDF File */
					$res = $eventModel->gernatePDF($userdata);
					/* Gernate Policy certificate PDF File */
					$certificate = $eventModel->gernateCertificatPDF($userdata);
					/* Gernate Policy Questionnaire Summery PDF File */
					$questionnaire = $eventModel->gernateQuestonnairePDF($userdata);
					/* OLD Method */
					// $pathArr = explode('/', $res);
					// $filename = $pathArr[count($pathArr)-1]; 
					// $filenamesave = $eventModel->savepdffilename($session->get('event_id'),$filename);

					/* New Method */
						/* For Invoice Policy */
						$PolicyPathArr = explode('/', $res);
						$PolicyFilename = $PolicyPathArr[count($PolicyPathArr)-1]; 
						$Policyfilenamesave = $eventModel->savepdffilename($session->get('event_id'),$PolicyFilename,'Policy');
						/* For Certificate Of Curruncy */
						$CertificatePathArr = explode('/', $certificate);
						$CertificateFilename = $CertificatePathArr[count($CertificatePathArr)-1]; 
						$Certificatefilenamesave = $eventModel->savepdffilename($session->get('event_id'),$CertificateFilename,'Certificate');
						/* For Questionnaire Summery */
						$QuestionnairePathArr = explode('/', $questionnaire);
						$QuestionnaireFilename = $QuestionnairePathArr[count($QuestionnairePathArr)-1]; 
						$Questionnairefilenamesave = $eventModel->savepdffilename($session->get('event_id'),$QuestionnaireFilename,'Questionnaire');

					$udata = unserialize($userdata->data);	
					if(!empty($udata['page4']['email'])){
						$urecipient = $udata['page4']['email'];
					} else {
						$urecipient = $udata['page1']['email'];
					}

					$this->userperfomers = $eventModel->getTerm('Perfomers');
					$this->userExtracharegs = $eventModel->getTerm('Excharge');
					$this->userannualincome = $eventModel->getTerm('AnnualIncome');
					$this->UserExtraCharegsByOrderId = $eventModel->getExtraFieldByOrder($session->get('event_id'));

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
					// $body .= "<div>";
					// $body .= "<div align='center'><img src='".JURI::base()."/images/new_logo.png' alt='Action Entertainment Insurance' title='Action Entertainment Insurance' /></div><br/><br/>";
					// $body .= "<div>Dear ".ucfirst($udata['page4']['firstname']).",</div><br/>";
					// $body .= "<div>Thank you for purchasing your insurance with Action Entertainment Insurance.</div><br/>";
					// $body .= "<div>We have pleasure enclosing the following documents which confirm cover:<br/>";
					// $body .= "<ul><li>Tax Invoice & Policy Schedule</li><li>Policy Wording</li><li>Certificate of Currency</li><li>Questionnaire Response Summary</li></ul></div><br/>";
					// $body .= "<div><table border='0'>";
					// $body .= "<tr>";
					// $body .= "<th align='left'>POLICY DETAILS</th>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Policy Number:</td>";
					// $body .= "<td>".$userdata->policy_number."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Order No:</td>";
					// $body .= "<td>".$userdata->id."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Name of Insured:</td>";
					// $body .= "<td>".$udata['page1']['insurencename']."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Activities:</td>";
					// $body .= "<td>";
					// 	$activity = '';
					// 	foreach ($udata['page1']['activity'] as $avalue) {
					// 		# code...
					// 		$activity .= $avalue.', ';
					// 	}
					// 	$body .= substr($activity,0,-2);

					// $body .= "</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Number of Performers:</td>";
					// $body .= "<td>";
					// 		foreach ($this->userperfomers as $pvalue) {
								
					// 			if($udata['page1']['perfomers'] == $pvalue->price){
					// 				$body .= $pvalue->name;
					// 			}
					// 		}
					// $body .= "</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Estimated Annual Income:</td>";
					// $body .= "<td>";
					// 		foreach ($this->userannualincome as $aincome) {
					// 			if($udata['page1']['annualincome'] == $aincome->price){
					// 				$body .= $aincome->name;
					// 			}
					// 		}
					// $body .= "</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Public Liability Cover:</td>";
					// $body .= "<td>$".$udata['page1']['liailitycover']."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Postcode:</td>";
					// $body .= "<td>".$udata['page1']['postcode']."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Start Date:</td>";
					// $body .= "<td>".$udata['page1']['start_date']."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Email:</td>";
					// $body .= "<td>".$udata['page1']['email']."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<th>&nbsp;</th>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<th align='left'>FEES:</th>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Premium:</td>";
					// $body .= "<td>$".number_format($udata['page2']['premium'],2)."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>GST Premium:</td>";
					// $body .= "<td>$".number_format($udata['page2']['gstpremium'],2)."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Stamp Duty:</td>";
					// $body .= "<td>$".number_format($udata['page2']['stampduty'],2)."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Broker Fee:</td>";
					// $body .= "<td>$".number_format($udata['page2']['brokerfee'],2)."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>GST Fee:</td>";
					// $body .= "<td>$".number_format($udata['page2']['gstfee'],2)."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'><b>Total Premium:</b></td>";
					// $body .= "<td><b>$";
					// $totalpremium = number_format($udata['page2']['premium'],2)+number_format($udata['page2']['gstfee'],2)+number_format($udata['page2']['brokerfee'],2)+number_format($udata['page2']['stampduty'],2)+number_format($udata['page2']['gstpremium'],2);
					// $body .= number_format($totalpremium,2)."</b>";
					// $body .= "</td>";
					// $body .= "</tr>";
					// if(!empty($TotalExtras)){
					// 	$body .= "<tr>";
					// 	$body .= "<td align='left'>Total Extras Charge:</td>";
					// 	$body .= "<td>$".number_format($TotalExtras,2)."</td>";
					// 	$body .= "</tr>";
					// 	$body .= "<tr>";
					// 	$body .= "<td align='left'>Sub Total:</td>";
					// 	$body .= "<td>$".number_format($udata['page6']['subtotal'],2)."</td>";
					// 	$body .= "</tr>";
					// }
					// $body .= "<tr>";
					// $body .= "<td align='left'>Credit Card Fee:</td>";
					// $body .= "<td>$".number_format($udata['page6']['creditcardfee'],2)."</td>";
					// $body .= "</tr>";
					// $body .= "<tr>";
					// $body .= "<td align='left'>Order Total:</td>";
					// $body .= "<td>$".number_format($udata['page6']['orderTotal'],2)."</td>";
					// $body .= "</tr>";
					// $body .= "</table></div><br/><br/><br/><br/>";
					// $body .= '<div align="center">';
					// $body .= '<div style="font-size:20px;"><b>ACTION ENTERTAINMENT INSURANCE PTY LTD</b></div>';
					// $body .= '<div>Corporate Representative No. 237473 as an authorised representative of Action Insurance Brokers P/L</div>';
					// $body .= '<div>ABN '.$this->abn.' AFS 225047</div>';
					// $body .= '<div>Suite 301, Building A, "Sky City", 20 Lexington Drive Bella Vista NSW 2153</div>';
					// $body .= '<div>Phone: 1300 655 424 Fax: (02) 8935 1501</div>';
					// $body .= '<div>Website: <a href="http://www.entertainmentinsurance.net.au">www.entertainmentinsurance.net.au</a></div>';
					// $body .= '<div>Email: <a href="mailto:entertainment@actioninsurance.com.au">entertainment@actioninsurance.com.au</a></div><br/>';
					// $body .= '</div></div>';
					// $body .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#8d8e90">
					//             <tr>
					//               <td>
					//                 <table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
					//                   <tr>
					//                     <td align="center">
					//                       <table width="100%" border="0" cellspacing="0" cellpadding="0">
					//                           <tr>
					//                             <td>
					//                               <table width="100%" border="0" cellspacing="0" cellpadding="0">
					//                                 <tr>
					//                                   <td height="46" align="right" valign="middle">
					//                                     <table width="100%" border="0" cellspacing="0" cellpadding="0">
					//                                       <tr>
					//                                         <td width="67%" align="right">
					//                                           <table width="100%" border="0" cellspacing="0" cellpadding="0">
					//                                             <tr>
					//                                               <td align="right"><a href="https://www.entertainmentinsurance.net.au/"><img src="'.JURI::base().'images/new_logo.png" width="598" height="186" alt="Action Entertainment" /></a></td>
					//                                             </tr>
					//                                           </table>                            
					//                                         </td>
					//                                       </tr>
					//                                     </table>
					//                                   </td>
					//                                 </tr>
					//                               </table>
					//                             </td>
					//                           </tr>
					//                       </table>            
					//                       <font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#68696a; font-size:22px; text-transform:uppercase"><strong>confirmation email</strong></font>
					//                     </td>
					//                   </tr>
					//                   <tr>
					//                       <td>
					//                         <table width="100%" border="0" cellspacing="0" cellpadding="0">
					//                           <tr>
					//                             <td width="7%">&nbsp;</td>
					//                             <td width="100" align="left" valign="top">
					//                               <table width="93%" border="0" cellspacing="0" cellpadding="0">
					//                                   <tr>
					//                                     <td style="font-size: 12px;"><p>Dear '.ucfirst($udata['page4']['firstname']).',</p>
					//                                         <p>Thank you for  purchasing your insurance with Action Entertainment Insurance. <br />
					//                                           We have  pleasure enclosing the following documents which confirm cover: </p>
					//                                           <ul>
					//                                             <li>Tax Invoice &amp; Policy Schedule</li>
					//                                             <li>Policy Wording</li>
					//                                             <li>Certificate of Currency</li>
					//                                             <li>Questionnaire Response Summary</li>
					//                                           </ul>
					//                                     </td>
					//                                   </tr>
					//                                   <tr>
					//                                     <td>&nbsp;</td>
					//                                   </tr>
					//                                   <tr>
					//                                     <td align="left" valign="top">
					//                                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
					//                                         <tr>
					//                                           <td width="45%" style="font-size: 12px;"><p><strong>POLICY DETAILS</strong><br />
					//                                             Policy Number:<br />
					//                                             Order No:<br />
					//                                             Name of  Insured:<br />
					//                                             Activities:<br />
					//                                             Number of  Performers:<br />
					//                                             Estimated  Annual Income:<br />
					//                                             Public  Liability Cover:<br />
					//                                             Postcode:<br />
					//                                             Start Date:<br />
					//                                             Email:</p>
					//                                           </td>
					//                                             <td width="55%" style="font-size: 12px;"><p><br />
					//                                               '.$userdata->policy_number.'<br />
					//                                               '.$userdata->id.'<br />
					//                                               '.$udata['page1']['insurencename'].'<br />';
					//                                               $activity = '';
					//                                               foreach ($udata['page1']['activity'] as $avalue) {
					//                                                 $activity .= $avalue.', ';
					//                                               }
					//                                               $body .= substr($activity,0,-2);
					//                                               $body .='<br />';
					//                                               foreach ($this->userperfomers as $pvalue) {
					//                                                 if($udata['page1']['perfomers'] == $pvalue->price){
					//                                                   $body .= $pvalue->price;
					//                                                 }
					//                                               }
					//                                               $body .= '<br />';
					//                                               foreach ($this->userannualincome as $aincome) {
					//                                                   if($udata['page1']['annualincome'] == $aincome->price){
					//                                                     $body .= $aincome->name;
					//                                                   }
					//                                                 }
					//                                               $body .= '<br />';
					//                                               if(!empty($udata['page1']['liailitycover']) && (strpos($udata['page1']['liailitycover'],'$') !== False)){
					// 													$publicliability = $udata['page1']['liailitycover'];
					// 												}else if(!empty($udata['page1']['liailitycover'])){
					// 													$publicliability ='$'.number_format($udata['page1']['liailitycover']);
					// 												}
					//                                               $body .= $publicliability;
					//                                               $body .= '<br />
					//                                               '.$udata['page1']['postcode'].'<br />
					//                                               '.$udata['page1']['start_date'].'<br />
					//                                               '.$udata['page1']['email'].'<br />
					//                                             </p>
					//                                             </td>
					//                                         </tr>
					//                                         <tr>
					//                                           <td width="45%">&nbsp;</td>
					//                                           <td width="55%">&nbsp;</td>
					//                                         </tr>
					//                                         <tr>
					//                                             <td valign="top" style="font-size: 12px;"><p><strong>FEES</strong><br />
					//                                               Premium: <br />
					//                                               GST Premium:<br />
					//                                               Stamp Duty:<br />
					//                                               Broker Fee:<br />
					//                                               GST Fee:</p>
					//                                               <p><strong>Total Premium:</strong><br />';
					//                                               if(!empty($TotalExtras)){ 
					//                                         $body .= '  Total Extras Charge:<br />
					//                                                     Sub Total:<br />';
					//                                               }
					//                                             $body .= 'Credit Card  Fee:<br />
					//                                                 Order Total:</p>
					//                                               </td>
					//                                               <td valign="top" style="font-size: 12px;"><p><br />
					//                                                 $'.number_format($udata['page2']['premium'],2).'<br />
					//                                                 $'.number_format($udata['page2']['gstpremium'],2).'<br />
					//                                                 $'.number_format($udata['page2']['stampduty'],2).'<br />
					//                                                 $'.number_format($udata['page2']['brokerfee'],2).'<br />
					//                                                 $'.number_format($udata['page2']['gstfee'],2).'                          </p>
					//                                                 <p><strong>$';
					//                                                 $totalpremium = number_format($udata['page2']['premium'],2)+number_format($udata['page2']['gstfee'],2)+number_format($udata['page2']['brokerfee'],2)+number_format($udata['page2']['stampduty'],2)+number_format($udata['page2']['gstpremium'],2);
					//                                                 $body .= number_format($totalpremium,2);
					//                                                 $body .= '</strong><br />';
					//                                                 if(!empty($TotalExtras)){ 
					//                                                 $body .= '$'.number_format($TotalExtras,2).'<br />';
					//                                                 $body .= '$'.number_format($udata['page6']['subtotal'],2).'<br />';
					//                                                 }
					//                                                 $body .= '$'.number_format($udata['page6']['creditcardfee'],2).'<br />
					//                                                   $'.number_format($udata['page6']['orderTotal'],2).'                          </p>
					//                                                   <p><br />
					//                                                   </p>
					//                                               </td>
					//                                         </tr>
					//                                       </table>
					//                                     </td>
					//                                   </tr>
					//                               </table>
					//                             </td>
					//                           </tr>
					//                           <td width="7%">&nbsp;</td>
					//                         </table>
					//                       </td>
					//                   </tr>
					//                   <tr>
					//                     <td style="font-size: 12px;"><p align="center"><strong>ACTION ENTERTAINMENT INSURANCE  PTY LTD<br />
					//                     </strong>Corporate Representative No. 237473 as an  authorised representative of Action Insurance Brokers P/L<br />
					//                     ABN '.$this->abn.' AFS 225047<br />
					//                     Suite 301, Building  A, &ldquo;Sky City&rdquo;, 20 Lexington Drive Bella Vista NSW 2153<br />
					//                     Phone: 1300 655 424<strong> </strong>Fax: (02) 8935 1501<br />
					//                     Website: <a href="http://www.entertainmentinsurance.net.au">www.entertainmentinsurance.net.au</a> <br />
					//                     Email: <a href="mailto:entertainment@actioninsurance.com.au">entertainment@actioninsurance.com.au</a></p></td>
					//                   </tr>
					//                   <tr><td>&nbsp;</td></tr>
					//                   <tr>
					//                     <td>
					//                       <img src="../../Free Email Templates - 99designs/Free Email Templates - 99designs/Green/NEWSLETTER GREEN/images/PROMO-GREEN2_07.jpg" width="598" height="7" style="display:block" border="0" alt=""/>
					//                     </td>
					//                   </tr>
					//                   <tr><td>&nbsp;</td></tr>
					//                   <tr>
					//                     <td>
					//                       <table width="100%" border="0" cellspacing="0" cellpadding="0">
					//                         <tr>
					//                           <td width="13%" align="center">&nbsp;</td>
					//                           <td width="14%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://www.entertainmentinsurance.net.au/" style="color:#010203; text-decoration:none"><strong>ABOUT</strong></a><a href= "http://yourlink" style="color:#010203; text-decoration:none"></a></font></td>
					//                           <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
					//                           <td width="9%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://www.entertainmentinsurance.net.au/products" style="color:#010203; text-decoration:none"><strong>PRODUCTS</strong></a><a href= "https://www.entertainmentinsurance.net.au/" style="color:#010203; text-decoration:none"></a></font></td>
					//                           <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
					//                           <td width="10%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://www.entertainmentinsurance.net.au/claims" style="color:#010203; text-decoration:none"><strong>CLAIMS </strong></a></font></td>
					//                           <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
					//                           <td width="11%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://www.entertainmentinsurance.net.au/contact-us" style="color:#010203; text-decoration:none"><strong>CONTACT </strong></a></font></td>
					//                           <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
					//                           <td width="17%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "https://yourlink" style="color:#010203; text-decoration:none"><strong>STAY CONNECTED</strong></a></font></td>
					//                           <td width="4%" align="right"><a href="https://www.facebook.com/ActionEntertainmentInsurance" target="_blank"><img src="'.JURI::base().'images/facebook.jpg" width="23" height="19" border="0" /></a></td>
					//                           <td width="5%" align="right"><a href="https://twitter.com/EntertainInsure" target="_blank"><img src="'.JURI::base().'images/twitter.jpg" alt="twitter" width="27" height="19" border="0" /></a></td>
					//                           <td width="4%" align="right"><a href="https://www.linkedin.com/company/action-entertainment-insurance-pty-ltd" target="_blank"><img src="'.JURI::base().'images/linkedin.jpg" width="25" height="19" border="0" /></a></td>
					//                           <td width="5%">&nbsp;</td>
					//                         </tr>
					//                       </table>
					//                     </td>
					//                   </tr>
					//                   <tr><td>&nbsp;</td></tr>
					//                 </table>
					//               </td>
					//             </tr>
					//          </table>';
					$body = "";
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
					$body .= '<td style="font-size: 12px;"><p>Dear '.ucfirst($udata['page4']['firstname']).',</p>';
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
					          '.$udata['page1']['insurencename'].'<br />';
					          $activity = '';
					          foreach ($udata['page1']['activity'] as $avalue) {
					           $activity .= $avalue.', ';
					          }
					$body .= substr($activity,0,-2);
					$body .='<br />';
					          foreach ($this->userperfomers as $pvalue) {
						          if($udata['page1']['perfomers'] == $pvalue->price){
						          $body .= $pvalue->price;
						          }
					          }
					$body .= '<br />';
					          foreach ($this->userannualincome as $aincome) {
						          if($udata['page1']['annualincome'] == $aincome->price){
						          $body .= $aincome->name;
						          }
					          }
					$body .= '<br />';
					          if(!empty($udata['page1']['liailitycover']) && (strpos($udata['page1']['liailitycover'],'$') !== False)){
								 $publicliability = $udata['page1']['liailitycover'];
							  }else if(!empty($udata['page1']['liailitycover'])){
								$publicliability ='$'.number_format($udata['page1']['liailitycover']);
							  }
					$body .= $publicliability;
					$body .= '<br />
					          '.$udata['page1']['postcode'].'<br />
					          '.$udata['page1']['start_date'].'<br />
					          '.$udata['page1']['email'].'<br />';
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
					          $'.number_format($udata['page2']['premium'],2).'<br />
					          $'.number_format($udata['page2']['gstpremium'],2).'<br />
					          $'.number_format($udata['page2']['stampduty'],2).'<br />
					          $'.number_format($udata['page2']['brokerfee'],2).'<br />
					          $'.number_format($udata['page2']['gstfee'],2).'</p>
					          <p><strong>$';
					          $totalpremium = number_format($udata['page2']['premium'],2)+number_format($udata['page2']['gstfee'],2)+number_format($udata['page2']['brokerfee'],2)+number_format($udata['page2']['stampduty'],2)+number_format($udata['page2']['gstpremium'],2);
								$body .= number_format($totalpremium,2);
								$body .= '</strong><br />';
					          if(!empty($TotalExtras)){ 
								$body .= '$'.number_format($TotalExtras,2).'<br />';
								$body .= '$'.number_format($udata['page6']['subtotal'],2).'<br />';
					          }
					          $body .= '$'.number_format($udata['page6']['creditcardfee'],2).'<br />
					          $'.number_format($udata['page6']['orderTotal'],2).'</p>';
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
					// echo $body;
					// die;         
					$mailData = array();
					$mailData['subject'] = "Test mail for paid by user";
					if($this->configemail){
							$mailData['sender'] = $this->configemail;	
					}else{
						$mailData['sender'] = $config->get( 'mailfrom' );	
					}
					$mailData['recipient'] = $urecipient;
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

					JFactory::getApplication()->enqueueMessage('Page six stored successfully');	
					$url = JRoute::_('index.php?option=com_event_manage&view=confirmation&step=7'.$sid);
					$app->redirect($url);
				}else if($hold_status){
					JFactory::getApplication()->enqueueMessage('Page six stored successfully');	
					$url = JRoute::_('index.php?option=com_event_manage&view=needassisantce&step=8'.$sid);
					$app->redirect($url);
				}else{
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

		if($this->frontdata != '' && isset($this->frontdata->data['page5'])){
			if($this->frontdata->data['page1']['start_date']){	
				if($this->frontdata->data['page5']['termconditions'] && $this->frontdata->data['page5']['fsgread']){
					$this->event  = $eventModel->getEventById($this->frontdata->data['page1']['event']);
					$this->state  = $eventModel->getFindState($this->frontdata->data['page1']['postcode']);		
					$this->premium = $eventModel->getPremium($this->frontdata->data['page2']['perfomers'],$this->state);
					$this->Pagesixfrontdata = $this->frontdata->data['page6'];
					$this->form->bind($this->Pagesixfrontdata);
				}else{
					// if($session->get('event_id')){
						JFactory::getApplication()->enqueueMessage('you dont accept our terms & condition','error');	
					// }
					if(!$user_id){
						$callback = '';
					}else{
						$callback = '&step=5'.$sid;
					}
					$url = JRoute::_('index.php?option=com_event_manage&view=declaration'.$callback);
					$app->redirect($url);
				}
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
				$callback = '&step=5'.$sid;
			}
			$url = JRoute::_('index.php?option=com_event_manage&view=declaration'.$callback);
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
		$this->perfomers = $eventModel->getTerm('Perfomers');
		$this->Extracharegs = $eventModel->getTerm('Excharge');
		$this->ExtraCharegsByOrderId = $eventModel->getExtraFieldByOrder($session->get('event_id'));
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
		$attachment = $mailData['attachment'];
		$attachment1 = $mailData['attachment1'];
		$attachment2 = $mailData['attachment2'];
		$attachment3 = $mailData['attachment3'];
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
}