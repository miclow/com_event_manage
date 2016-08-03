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
class Event_manageViewConfirmation extends JViewLegacy
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
		$this->sid = JRequest::getVar('sid');
		$jinput = JFactory::getApplication()->input;
		
		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->chargecurrency = $componentParams->get('chargecurrency');
		$this->insurername = $componentParams->get('insurername');
		$this->insureraddress = $componentParams->get('insureraddress');
		$this->abn = $componentParams->get('abn');

		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');

        $formname = array('checkout');
        $this->form = $formModel->getForm($formname);
        $this->pageArr = array('event'=>'1','quotation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','checkout'=>'6');
		
		if($user_id){
			$this->userdata = $eventModel->getUserDataById($user_id);
			$this->progress = $eventModel->getSessionDataById( $this->userdata->id );
			if($session->get('event_id') == 'NULL'){
				$session->set('event_id', $this->userdata->id);
			}
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
			
			if(!empty($this->sid)){
				$sid = '&sid='.$this->sid;
			}

		$this->errors = '';
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {			
			/*$data['terms'] = $jinput->post->get('terms','');
			$data['event'] = $jinput->post->get('event','');	*/

			if($this->form->validate($formdata)){	
					
				$data['onpage']= '6';
				$data['create_date']= date('d-m-Y');
				$data['data']['page1'] = $this->PageOnedata->data['page1'];	
				$data['data']['page2'] = $this->PageOnedata->data['page2'];
				$data['data']['page3'] = $this->PageOnedata->data['page3'];
				$data['data']['page4'] = $this->PageOnedata->data['page4'];
				$data['data']['page5'] = $this->PageOnedata->data['page5'];
				$data['data']['page6']['creditcard'] = $formdata['creditcard'];
				$data['data']['page6']['cardnumber'] = $formdata['cardnumber'];
				$data['data']['page6']['expirationdate'] = $formdata['expirationdate'];
				$data['data']['page6']['CVV'] = $formdata['CVV'];
				$data['data']['page6']['startdate'] = $formdata['startdate'];
				$data['data']['page6']['publicliailitycover'] = $formdata['publicliailitycover'];
				$data['data']['page6']['totalpremium'] = $formdata['totalpremium'];
		    	$data['status'] = '1';	
		    	
		    	$ee = $eventModel->setEventsAndTerms($data);	    	
				if($ee != false ){
					JFactory::getApplication()->enqueueMessage('Page six store succesfully');	
					/*$url = JRoute::_('index.php?option=com_event_manage&view=confirmation&step=7');
					$app->redirect($url);*/

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

		if($this->frontdata != '' && isset($this->frontdata->data['page6'])){			
			$this->event  = $eventModel->getEventById($this->frontdata->data['page1']['event']);
			$this->state  = $eventModel->getFindState($this->frontdata->data['page1']['postcode']);		
			$this->premium = $eventModel->getPremium($this->frontdata->data['page2']['perfomers'],$this->state);
			$this->Pagesixfrontdata = $this->frontdata->data['page6'];
			$this->form->bind($this->Pagesixfrontdata);
		}else{
			if($session->get('event_id')){
				JFactory::getApplication()->enqueueMessage('Select this form first');	
			}	
			$url = JRoute::_('index.php?option=com_event_manage&view=checkout&step=6'.$sid);
			$app->redirect($url);
		}


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
		$session->clear('event_id');
		$this->Extrachreges = $eventModel->getTerm('Excharge');
		parent::display($tpl);					
	}


}