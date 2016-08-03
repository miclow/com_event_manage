<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Event_manage
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Class Event_manageController
 *
 * @since  1.6
 */
class Event_manageController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/event_manage.php';

		$view = JFactory::getApplication()->input->getCmd('view', 'events');
		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}

	public function checkUserExtrafieldById(){

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$post = JRequest::get( 'post' );
			// if(isset($post['type']) && $post['type'] == 'checkUserName'){

				$db =& JFactory::getDBO();
				$query = "SELECT name,price,description FROM #__event_manage_term WHERE id = '".$post['allextrafield']."' and name NOT IN ('premium','brokerfee')";
				$db->setQuery($query);
				$checkId = $db->loadObject();		
				$return = '';				
				if ($checkId)
				{
				    $return  = array('status' => 'found','name'=>$checkId->name,'price'=>$checkId->price,'description'=>$checkId->description);
				    // $return  = array('status' => 'found','name'=>$checkId);
				}else{
					$return  = array('status' => 'notfound');
				}
				// echo json_encode($post);
				echo json_encode($return);
				exit();				
			// }

		}
		exit('dsdsds');
	}

	public function checkUserName(){
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$post = JRequest::get( 'post' );
			if(isset($post['type']) && $post['type'] == 'checkUserName'){

				$db =& JFactory::getDBO();
				$query = "SELECT COUNT(*) FROM #__users WHERE email = '".$post['email1']."'";
				$db->setQuery($query);
				$checkId = $db->loadResult();		
				$return = '';				
				if ($checkId)
				{
				    $return  = array('status' => 'found');
				}else{
					$return  = array('status' => 'notfound');
				}
				echo json_encode($return);
				exit();				
			}

		}
		exit('dsdsds');
	}
	
	public function checkUserNameByEmail(){
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$post = JRequest::get( 'post' );

				$app = JFactory::getApplication()->input;		
				$db = JFactory::getDBO();
				$query = "SELECT COUNT(*) FROM #__users WHERE email = '".$post['jform']['confirmemail']."'";
				$db->setQuery($query);
				$checkId = $db->loadResult();		
				$return = '';				
				if ($checkId){
				    $return  = array('status' => 'found');
				}else{
					$return  = array('status' => 'notfound');
				}
				echo json_encode($return);
				exit();				

		}
		exit('dsdsds');
	}

	public function changeUnpaidToPaid(){
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$post = JRequest::get( 'post' );
				$pageArr = array('event'=>'1','quotation'=>'2','proposal'=>'3','address'=>'4','declaration'=>'5','Checkout'=>'6');
				$app = JFactory::getApplication()->input;		
				$session = JFactory::getSession();
				$db = JFactory::getDBO();
				$query = "SELECT onpage,activity_status,data FROM #__event_manage_session WHERE id = '".$post['pid']."'";
				$db->setQuery($query);
				$checkId = $db->loadObject();	
				$return = '';				
				if($checkId->onpage == 1){
					$data = unserialize($checkId->data);
					if($data['page1']['perfomers'] == '13+' && $data['page1']['annualincome'] == '$250,000+'){
						// $performer = $data['page1']['perfomers'];
						$query = "SELECT COUNT(*) FROM #__event_manage_term WHERE activity = 'Excharge' AND order_id = '".$post['pid']."'";
						$query .= " AND name IN ('premium','brokerfee')";
						$db->setQuery($query);
						$result = $db->loadResult();
						if(!$result){
							$referral = $data['page1']['perfomers'];
						}else{
							$referral = $data['page1']['annualincome'];	
						}	
					}else if($data['page1']['perfomers'] == '13+'){
						$referral = $data['page1']['perfomers'];
					}else if($data['page1']['annualincome'] == '$250,000+'){
						$referral = $data['page1']['annualincome'];
					}
				}
				
				$html = '';
				/* Get Extra field data */
				$equery = "SELECT id,name,price,description FROM  #__event_manage_term WHERE activity = 'Excharge'" ;
				$equery .= " AND name NOT IN ('premium','brokerfee') AND referral_page_no=".$checkId->onpage." group by name,referral_page_no";
				$db->setQuery($equery);
				$this->AllExtraField = $db->loadObjectList();
				if(empty($this->AllExtraField)){
					$equery = "SELECT id,name,price,description FROM  #__event_manage_term WHERE activity = 'Excharge'" ;
					$equery .= " AND name NOT IN ('premium','brokerfee') group by name,referral_page_no";
					$db->setQuery($equery);
					$this->AllExtraField = $db->loadObjectList();
				}
					$html .='<select name="allextrafield" id="allextrafield" required="true" class="allextrafield">';
					$html .='<option value="">Select value</option>';
								if(!empty($this->AllExtraField) && count($this->AllExtraField) > 0)
								foreach ($this->AllExtraField as $extravalue) {
							
					$html .='<option value="'.$extravalue->id.'">'.$extravalue->name.' - '.$extravalue->price.'</option>';
							 }
					$html .='</select>';
				/* End Get Extra field data */

				$OrderDetail['orderid'] = $post['pid'];
				$OrderDetail['onpage'] = $checkId->onpage;
				
				$session->set('OrderDetail',$OrderDetail);

				if ($checkId){	
					foreach ($pageArr as $key => $value){
						if($value == $checkId->onpage){
							$page = ucfirst($key);
						}
					}
				    $return  = array('status' => $checkId->activity_status,'referral'=> $referral,'page'=> $page,'onpage'=> $checkId->onpage,'extraField'=>$html);
				}else{
					$return  = array('status' => 'notfound');
				}
				echo json_encode($return);
				exit();				

		}
		exit('dsdsds');
	}

}