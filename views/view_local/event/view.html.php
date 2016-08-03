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

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * View to edit
 *
 * @since  1.6
 */
class Event_manageViewEvent extends JViewLegacy
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
		$eventModel = JModelLegacy::getInstance('event','Event_manageModel');
		$session = JFactory::getSession();
		$user = & JFactory::getUser();	
		$user_id = $user->get( 'id' );
		$step = JRequest::getVar('step');
		$jinput = JFactory::getApplication()->input;
		$app = JFactory::getApplication();
		if(!empty($user_id)){
			$this->sid = JRequest::getVar('sid');
		}else{
			$this->sid = '';
		}


		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');
		$session_model = JModelLegacy::getInstance('session', 'Event_manageModel');
		$this->pageArr = array('event'=>'1','quotation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','checkout'=>'6');
		$this->pageKey = array('page1','page2','page3','page4','page5','page6','page7');
		$this->stepArr = array('new','back');
		$formname = array('event');
		$this->form = $formModel->getForm($formname);
		$this->Olduser = '';		
		if($user_id){
			if(isset($this->sid)){
				$this->userdata = $eventModel->getSessionDataById($this->sid);
			}else{
				$this->userdata = $eventModel->getUserDataById($user_id);
			}
 
			if($step == 'back' && $this->sid != ""){
				$this->Olduser = 1;
			}else if($step != 'new'){
				if($this->userdata->onpage != 6 ){
					$this->Olduser = 1;
				}
			}else{
				$this->Olduser = 2;
			}

			if($this->Olduser == 1){
				$this->progress = $eventModel->getSessionDataById( $this->userdata->id );
				$session->set('event_id', $this->userdata->id);
			}else{
				$session->clear("event_id");
				$session->clear("allpagedata");
				$session->clear("activereferral");
				$session->clear("referraldata");
			}

			
		}else{
			$this->progress = $eventModel->getSessionDataById($session->get('event_id'));
		}

		$this->PageOnedata = '';
		if($session->get('event_id', 'NULL') != 'NULL'){
			$this->PageOnedata = $session_model->getItem($session->get('event_id'));						
			$this->PageOnedata->data = unserialize($this->PageOnedata->data);				
		} 
		$this->errors = '';
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						
			$formdata = JRequest::getVar('jform');

			$this->form->bind($formdata);

			if($this->form->validate($formdata)){	
				
				$data['onpage']= '1';
				$data['create_date']= date('d-m-Y');
				$data['data']['page1']['insurencename'] = $formdata['insurencename'];		
				$data['data']['page1']['activity'] = $formdata['activity'];
				$data['data']['page1']['perfomers'] = $formdata['perfomers'];
				$data['data']['page1']['annualincome'] = $formdata['annualincome'];
				$data['data']['page1']['postcode'] = $formdata['postcode'];		
				$data['data']['page1']['email'] = $formdata['email'];
				$data['data']['page1']['confirmemail'] = $formdata['confirmemail'];						
				$data['data']['page1']['start_date'] = $formdata['start_date'];
				$data['data']['page1']['liailitycover'] = $formdata['liailitycover'];
				foreach ($this->pageKey as $value) {
				 	if($value != 'page1'){
						if(array_key_exists($value, $this->PageOnedata->data)){
							$data['data'][$value] = $this->PageOnedata->data[$value];
							$data['onpage'] = $this->PageOnedata->onpage;		
						}
					}
				}
		    	$data['status'] = '1';	
		    	$data['activity_status'] = 'Unfinished';

		  //   	echo "<pre>";
				// print_r($this->PageOnedata->data);
				// print_r($formdata);
				// print_r($data);
				// echo "</pre>";
				// die;
		    	
				$mesgArr = '';
		    	if($formdata['perfomers'] == '13+'){
		    		$mesgArr[] = 'perfomers';
		    	}
		    	if($formdata['annualincome'] == '$250,000+'){
		    		$mesgArr[] = 'annualincome';	
		    	}
		    	
		    	if(!empty($this->sid)){
						$sid = '&sid='.$this->sid;
				}
				
				$maxExtrafield = $eventModel->getNumberOfExtraFieldOrderWise($session->get('event_id'),$data['onpage'],$formdata['perfomers']);
				
				if(!empty($step) && $step != 'new'){
					$this->activitystatus = $eventModel->getActivityStatusByOrderId($this->sid);
				}else{
					$this->activitystatus->activity_status = ''	;
				}
				
				if(!$maxExtrafield || $this->activitystatus->activity_status == 'Referral'){
			    	if($formdata['perfomers'] == '13+'){
			    		$session->set('allpagedata', $data);
			    		$session->set('referraldata', $formdata);
			    		$session->set('activereferral', $mesgArr);
						$url = JRoute::_('index.php?option=com_event_manage&view=referral&step=6b'.$sid);
						$app->redirect($url);
			    	}
			    	if($formdata['annualincome'] == '$250,000+'){
			    		$session->set('allpagedata', $data);
			    		$session->set('referraldata', $formdata);
			    		$session->set('activereferral', $mesgArr);
						$url = JRoute::_('index.php?option=com_event_manage&view=referral&step=6b'.$sid);
						$app->redirect($url);
			    	}
		    	}
	
		    	$this->state =$eventModel->getfindState($formdata['postcode']);
		    	if(!empty($this->state)){
			    	
			    	/*$ee = $eventModel->setEventsAndTerms($data);*/
			    	if(!empty($this->sid)){
						$ee = $eventModel->UpdateEventsAndTerms($data,$this->sid);
						$sid = '&sid='.$this->sid;
					}else{
						$ee = $eventModel->setEventsAndTerms($data);	    	
					} 

					if($ee != false ){
						JFactory::getApplication()->enqueueMessage('Page one store succesfully');	
						$url = JRoute::_('index.php?option=com_event_manage&view=quotation&step=2'.$sid);
						$app->redirect($url);
					}
				}else{
				
					$this->errors = '<ul class="server_error">';
					$this->errors .= '<li>postcode not valid</li>';
					$this->errors .= '</ul>';
				}
			}else{
				
				$this->errors = '<ul class="server_error">';
				
				foreach ($this->form->getErrors() as $key => $value) {
					$this->errors .= '<li>'.$value->getMessage().'</li>';
				}				
				$this->errors .= '</ul>';
			}		
		}


		if($session->get('referraldata') != 'NULL'){
			$session->clear("allpagedata");
			$session->clear("activereferral");
    		$backreferraldata = $session->get( 'referraldata' );
    		$this->form->bind($backreferraldata);
	    }
		    	

		$this->front = '';
		if($session->get('event_id', 'NULL') != 'NULL'){
			$this->frontdata = $session_model->getItem($session->get('event_id'));						
			$this->frontdata->data = unserialize($this->frontdata->data);	

			if($user_id != $this->frontdata->user){
				$session->get('event_id',NULL);
				$data = NULL;
			}		

			$this->frontdata = (Array) $this->frontdata->data['page1'];
			/*print_r($this->frontdata);*/
			$this->form->bind($this->frontdata);
			$this->front['activity'] = $this->frontdata['activity'];
		}

		$this->events = $this->get('Events');			
		$this->terms = $this->get('Term');	
		$this->activity = $this->get('Activity');
		$this->perfomers = $this->get('Perfomers');
		$this->annualincome = $this->get('AnnualIncome');
		$this->lcover = $this->get('LiailityCover');
		if($user_id){

			$this->frontdatadata = unserialize($this->progress->data);	
			$this->frontdatadata = (Array) $this->frontdatadata['page1'];
			
				if(!isset($step)){		
					foreach ($this->pageArr as $key => $value){
							$step = $value; 
							$url = JRoute::_('index.php?option=com_event_manage&view=dashboard');
							$app->redirect($url);
					}
				}else if(isset($step)){		
				   if(!in_array($step, $this->stepArr)){
						foreach ($this->pageArr as $key => $value){
							if($value == $this->progress->onpage){		
								$step = $value; 
								if($this->Olduser == 2){		
									$url = JRoute::_('index.php?option=com_event_manage&view=event&step=1');
									$app->redirect($url);
								}
								if($key != 'event'){
									$url = JRoute::_('index.php?option=com_event_manage&view='.$key.'&step='.$step);
									$app->redirect($url);
								}
							}
						}
					}
				}
			
			
			$this->form->bind($this->frontdatadata);
		}

		parent::display($tpl);
	}


}