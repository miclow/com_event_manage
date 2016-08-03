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
class Event_manageViewUsersdetail extends JViewLegacy
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
		$this->show = JRequest::getVar('show');
		$status = JRequest::getVar('status');
		$id = JRequest::getVar('id');
		$thsi->groups = JUserHelper::getUserGroups($user_id);

		$jinput = JFactory::getApplication()->input;
		
		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');

        /*$formname = array('quotation');
        $this->form = $formModel->getForm($formname);*/
        $this->pageArr = array('event'=>'1','quotation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','checkout'=>'6');
		
/*		if($user_id){
			$this->userdata = $eventModel->getUserDataById($id);
			$this->progress = $eventModel->getSessionDataById( $this->userdata->id );
			$session->set('event_id', $this->userdata->id);
		}else{
			$this->progress = $eventModel->getSessionDataById($id);
		}

		$this->PageOnedata = '';
		if($session->get('event_id', 'NULL') != 'NULL'){
			$this->PageOnedata = $session_model->getItem($session->get('event_id'));						
			$this->PageOnedata->data = unserialize($this->PageOnedata->data);				
		} */    


			
		$this->errors = '';
		
		$this->userdetail = $eventModel->getSessionDataById($id);
		$this->perfomers = $eventModel->getTerm('Perfomers');
		$this->annualincome = $eventModel->getTerm('AnnualIncome');
		$this->statename = $eventModel->getStateName();

		$autorizedid = array('6','8');
		if(!$user_id){
			$url = JRoute::_('index.php?option=com_event_manage&view=event');
			$app->redirect($url);
		}
		// foreach ($thsi->groups as $gid){
		// 	if(!in_array($gid, $autorizedid)){
		// 		foreach ($this->pageArr as $key => $value){
		// 			if($value == $this->userdetail->onpage){		
		// 				$step = $value; 									
		// 				$url = JRoute::_('index.php?option=com_event_manage&view='.$key.'&step='.$step);
		// 				$app->redirect($url);
		// 			}
		// 		}
		// 	}
		// }
		parent::display($tpl);					
	}


}