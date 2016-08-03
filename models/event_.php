<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Event_manage
 * @author     jainik <jainik@raindropsinfotech.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jimport('joomla.form.helper');
jimport( 'joomla.user.helper' );
jimport( 'joomla.user.user' );
JFormHelper::loadFieldClass('list');

/**
 * Event_manage model.
 *
 * @since  1.6
 */
class Event_manageModelEvent extends JModelLegacy
{

	public function test(){
		echo 'call';
	}

	function getEvents(){
		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_event` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Filter by published state
		$published = 1;//$this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;
	}

	function getTerm($Term = null){

		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_term` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if(!empty($Term)){
			$query->where('a.activity = "'.$Term.'"');
		}

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		
		/*echo $query;*/
		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;

	}

	function getTermByName($Term = null){

		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_term` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if(!empty($Term)){
			$query->where('a.name = "'.$Term.'"');
		}

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		
		/*echo $query;*/
		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;

	}

	function getTermByValueAndGroup($option,$group){

		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_term` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if(!empty($option)){
			$query->where('a.price = "'.$option.'"');
		}

		if(!empty($group)){
			$query->where('a.activity = "'.$group.'"');
		}

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		
		/*echo $query;*/
		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;

	}

	function getFindState($postcode) {
	  $ranges = array(
	    'NSW' => array(
	      1000, 1999,
	      2000, 2599,
	      2619, 2898,
	      2921, 2999
	    ),
	    'ACT' => array(
	      200, 299,
	      2600, 2618,
	      2900, 2920
	    ),
	    'VIC' => array(
	      3000, 3999,
	      8000, 8999
	    ),
	    'QLD' => array(
	      4000, 4999,
	      9000, 9999
	    ),
	    'SA' => array(
	      5000, 5999
	    ),
	    'WA' => array(
	      6000, 6797,
	      6800, 6999
	    ),
	    'TAS' => array(
	      7000, 7999
	    ),
	    'NT' => array(
	      800, 999
	    )
	  );
	  $exceptions = array(
	    872 => 'NT',
	    2540 => 'NSW',
	    2611 => 'ACT',
	    2620 => 'NSW',
	    3500 => 'VIC',
	    3585 => 'VIC',
	    3586 => 'VIC',
	    3644 => 'VIC',
	    3707 => 'VIC',
	    2899 => 'NSW',
	    6798 => 'WA',
	    6799 => 'WA',
	    7151 => 'TAS'
	  );

	  $postcode = intval($postcode);
	  if ( array_key_exists($postcode, $exceptions) ) {
	    return $exceptions[$postcode];
	  }

	  foreach ($ranges as $state => $range)
	  {
	    $c = count($range);
	    for ($i = 0; $i < $c; $i+=2) {
	      $min = $range[$i];
	      $max = $range[$i+1];
	      if ( $postcode >= $min && $postcode <= $max ) {
	        return $state;
	      }
	    }
	  }

	  return null;
	
	}

	function getActivity(){

		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_term` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		$query->where("a.activity = 'Activity'");

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;

	}

	function getPerfomers(){

		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_term` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		$query->where("a.activity = 'Perfomers'");

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;

	}

	function getAnnualIncome(){

		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_term` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		$query->where("a.activity = 'AnnualIncome'");

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;

	}

	function getLiailityCover(){

		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_term` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		$query->where("a.activity = 'LiailityCover'");

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;

	}

	function getPremium($perfomer,$postalstate){

		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_premium` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if(!empty($perfomer)){
			$query->where('a.performers = "'.$perfomer.'"');
		}

		if(!empty($postalstate)){
			$query->where('a.postalstate = "'.$postalstate.'"');
		}

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;

	}

	function getExtrachreges(){

		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_term` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		$query->where("a.activity = 'EXCHARGE'");

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;

	}

	function setEventsAndTerms($data){

		$session =& JFactory::getSession();
		$data['id'] = 0;
		if($session->get('event_id', 'NULL') != 'NULL'){
			$data['id']= $session->get('event_id');						
		}
			
		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $session_model = JModelLegacy::getInstance('session', 'Event_manageModel');              
        
        	
        //$dd = $session_model->getItem($pk);	
        $user = & JFactory::getUser();
        $Uid = $user->get( 'id' );
        
        if(!empty($Uid)){				
			$data['user'] = $user->get( 'id' );        					  		
		}else if(!empty($data['Uid'])){
			$data['user'] = $data['Uid'];
		}

	    $data['data']= serialize($data['data']);	
	    $data['session_id']= '';		    
	   /* print_r($data); 
	    die;*/
	    $dd = $session_model->save($data);

	    if($data['id'] == 0){
	    	$session->set('event_id', $dd);
		}

		if(!empty($data['Uid'])){
			$usersave = $this->updateEventUserId($data['Uid'],$data['Eid']);			
		}

		if($usersave){
			$UC = 'update';
			return $UC;	
		}else{
			return true;
		}	                    	       
	
	}

	function UpdateEventsAndTerms($data,$sid){
  	
        //$dd = $session_model->getItem($pk);	
        $user = & JFactory::getUser();
        $Uid = $user->get( 'id' );
        $sql = '';
        if($data['onpage'] == 6){
        	$sql .= ' `sp_statusCode` = "'.$data['sp_statusCode'].'",';
        	$sql .= ' `sp_statusDescription` = "'.$data['sp_statusDescription'].'",';
        	$sql .= ' `sp_txnSource` = "'.$data['sp_txnSource'].'",';
        	$sql .= ' `sp_purchaseOrderNo` = "'.$data['sp_purchaseOrderNo'].'",';
        	$sql .= ' `sp_approved` = "'.$data['sp_approved'].'",';
        	$sql .= ' `sp_thinlinkResponseCode` = "'.$data['sp_thinlinkResponseCode'].'",';
        	$sql .= ' `sp_thinlinkResponseText` = "'.$data['sp_thinlinkResponseText'].'",';
        	$sql .= ' `sp_thinlinkEventStatusCode` = "'.$data['sp_thinlinkEventStatusCode'].'",';
        	$sql .= ' `sp_thinlinkEventStatusText` = "'.$data['sp_thinlinkEventStatusText'].'",';
        	$sql .= ' `sp_recurring` = "'.$data['sp_recurring'].'",';
        	$sql .= ' `sp_actionType` = "'.$data['sp_actionType'].'",';
        	$sql .= ' `sp_clientID` = "'.$data['sp_clientID'].'",';
        	$sql .= ' `sp_responseCode` = "'.$data['sp_responseCode'].'",';
        	$sql .= ' `sp_responseText` = "'.$data['sp_responseText'].'",';
        	$sql .= ' `sp_successful` = "'.$data['sp_successful'].'",';
        	$sql .= ' `sp_txnType` = "'.$data['sp_txnType'].'",';
        	$sql .= ' `sp_amount` = "'.$data['sp_amount'].'",';
        	$sql .= ' `sp_currency` = "'.$data['sp_currency'].'",';
        	$sql .= ' `sp_txnID` = "'.$data['sp_txnID'].'",';
        	$sql .= ' `sp_receipt` = "'.$data['sp_receipt'].'",';
        	$sql .= ' `sp_ponum` = "'.$data['sp_ponum'].'",';
        	$sql .= ' `sp_settlementDate` = "'.$data['sp_settlementDate'].'",';
        	$sql .= ' `sp_orderid` = "'.$data['sp_orderid'].'",';
        }
       
		/*$session->set('event_id', $sid);*/
		$db = JFactory::getDBO();
		$query = "UPDATE `#__event_manage_session` SET `user`= '".$Uid."',
														`data`='".serialize($data['data'])."',
														`onpage`='".$data['onpage']."',
														`status`='".$data['status']."',
														 $sql
														`activity_status`='".$data['activity_status']."'
														 WHERE `id` = '".$sid."'";
		$db->setQuery($query);
		$checkId = $db->execute();	
		if($checkId){
			if(!empty($data['Uid'])){
				$UC = 'update';
				return $UC;
			}else{
				return true;
			}
		}else{
			return false;
		}	                	       
	
	}

	function updateEventUserId($uid,$eid){

		$db = JFactory::getDBO();
		$query = "UPDATE `#__event_manage_session` SET `user`= '".$uid."'  WHERE `id` = '".$eid."'";
		$db->setQuery($query);
		$checkId = $db->execute();		
		if($checkId){
			return true;
		}else{
			return false;
		}	                           
	
	}

	function getEventById($eventId){
		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $eventModel = JModelLegacy::getInstance('events', 'Event_manageModel');            
        $return  =  $eventModel->getItems($eventId);	          
        return $return[0];
	}

	function getTermById($termId){
		JModelLegacy::addIncludePath(JPATH_BASE.'/administrator/components/com_event_manage/models');	
        $termModel = JModelLegacy::getInstance('term', 'Event_manageModel');                    
        return $termModel->getItem($termId);	                  
	}

	function getSessionDataById($userid){
		
        // Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_session` AS a');

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if(!empty($userid)){
			$query->where('a.id = "'.$userid.'"');
		}

		if (is_numeric($published))
		{
			$query->where('a.status = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.status IN (0, 1))');
		}

		$query->order('a.id DESC');

		/*echo $query;*/
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result;                  
	}

	function getUserDataById($userid){
		
        // Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__event_manage_session` AS a');

		// Filter by published state
		$published = 1; //$this->getState('filter.state');

		if(!empty($userid)){
			$query->where('a.user = "'.$userid.'"');
		}

		if (is_numeric($published))
		{
			$query->where('a.status = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.status IN (0, 1))');
		}

		$query->order('a.id DESC');

		/*// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}*/
		
		/*echo $query;*/
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result;                  
	}

	function getDataById($userid){
		
      // Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query = "SELECT b.* FROM #__event_manage_session AS b WHERE b.user = $userid ORDER BY b.id DESC";
		
		//echo $query;
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result;                  
	}

	function getStateName(){
		
      // Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query = "SELECT s.* FROM #__state AS s WHERE s.countryid = 15";
		
		//echo $query;
		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;                  
	}

	function getUserDetailByEmail($email){
		
        // Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT u.*'
			)
		);
		$query->from('`#__users` AS u');

		if(!empty($email)){
			$query->where('u.email = "'.$email.'"');
		}

		$query->where('u.block = 0');
		
		$query->order('u.id DESC');
		
		/*echo $query;*/
		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;                  
	}
	
	function getAllUserDetail(){
		
        // Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query = "SELECT DISTINCT a.id ,a.*, b.* FROM #__users AS a INNER JOIN #__event_manage_session AS b ON b.user = a.id LEFT JOIN #__user_usergroup_map AS ug ON ug.user_id = a.id WHERE a.block = 0 AND ug.group_id NOT IN (6,8) GROUP BY a.id ORDER BY a.id DESC";
		
		/*echo $query;*/
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;                  
	}

	function setCreateNewUser($userdetail){

		$config = JFactory::getConfig();
		$db		= JFactory::getDbo();
		$params = JComponentHelper::getParams('com_users');
		JPluginHelper::importPlugin('user');
		$app = JFactory::getApplication();
		$mailer = JFactory::getMailer();
		$sender = array( 
		    $config->get( 'mailfrom' ),
		    $config->get( 'fromname' ) 
		);
		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->configemail = $componentParams->get('email');
		// Initialise the table with JUser.
		$user = new JUser;

		/*print_r($userdetail);
		die;*/
		if(!empty($userdetail['page1']['insuredname'])){
			$userdetail['page1']['insurencename'] = $userdetail['page1']['insuredname'];	
		}
		$data['name']		= $userdetail['page1']['insurencename'];
		$data['username']	= $userdetail['page1']['email'];
		$data['email']		= $userdetail['page1']['email'];
		$data['password']	= JUserHelper::genRandomPassword();
		$data ['activation'] = '0';
		$data ['block'] = 0;
		$system	= $params->get('new_usertype', 2);
		$data['groups'] = array($system);
		$data['siteurl'] = JURI::root();
		$data['sitename']	= $config->get('sitename');
		$username = $data['email'];
		$password = $data['password'];
		// Bind the data.
		if (! $user->bind ( $data )) {
			echo "Not bind data";
		}
		
		// Load the users plugin group.
		
		// Store the data.
		if (!$user->save()){
			$resgister = 1;
		}


		$emailSubject	= JText::sprintf(
				'user detail',
				$data['name'],
				$data['sitename']
			);

		$emailBody  = 'name :'.$data['name'].'<br>';
		$emailBody .= 'sitename :'.$data['sitename'].'<br>';
		$emailBody .= 'siteurl :'.$data['siteurl'].'<br>';
		$emailBody .= 'username :'.$data['username'].'<br>';
		$emailBody .= 'password :'.$password;

		/*if($resgister){
			$sendmail = JFactory::getMailer()->sendMail($config->get( 'mailfrom' ), $config->get( 'fromname' ), $data['email'], $emailSubject, $emailBody);
		}*/
		if(!$resgister){
			if($this->configemail){
				$sender = array($this->configemail , $config->get( 'fromname' ));	
			}
			$mailer->setSender($sender);
			$recipient = array( $data['email'] );
			$mailer->addRecipient($recipient);
			$mailer->setSubject('Testing email by rainddrops');
			$mailer->setBody($emailBody);
			$send = $mailer->Send();
		}

		$userArr = array($username,$password,$user->id);

		if(!$resgister){
			return $userArr;
		}else{
			return false;
		}
		
		/*if ($send !== true ) {
		    echo 'Mail not sent ';
		} else {
		    echo 'Mail sent';
		}*/


/*		if($sendmail !== false){
			$url = JRoute::_('index.php?option=com_event_manage&view=quotation&step=2');
			$app->redirect($url);
		}else{
			return false;
		}*/
		
	}

	function getDataByIdForDashborad($userid){
		
      // Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query = "SELECT a.*, b.* FROM #__users AS a INNER JOIN #__event_manage_session AS b ON b.user = a.id LEFT JOIN #__user_usergroup_map AS ug ON ug.user_id = a.id WHERE a.block = 0 AND a.id = $userid AND ug.group_id IN (2) ORDER BY b.activity_status ";
		//echo $query;
		$db->setQuery($query);
		$result = $db->loadObjectlist();
	
		return $result;                  
	}
}
