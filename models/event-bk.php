<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Event_manage
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

	function __construct(){
		 parent::__construct();
		 // Set the pagination request variables
		 $this->setState('limit', JRequest::getVar('limit', 20, '', 'int'));
		 $this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
		 $this->setState('s_ordernumber', JRequest::getVar('s_ordernumber','','post'));
		 $this->setState('s_policynumber', JRequest::getVar('s_policynumber','','post'));
		 $this->setState('s_firstname', JRequest::getVar('s_firstname','','post'));
		 $this->setState('s_lastname', JRequest::getVar('s_lastname','','post'));
		 $this->setState('s_email', JRequest::getVar('s_email','','post'));


	}

	public function getPagination(){

		$app		= JFactory::getApplication();
		$limit 	= $app->getUserStateFromRequest('com_event_manage.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_event_manage.limitstart', 'limitstart', 0, 'int');
		$db    = $this->getDbo();
		// $db->setQuery("SELECT COUNT(*) FROM #__event_manage_session WHERE status = 1");

		$searchArr['s_ordernumber'] = $this->getState('s_ordernumber');
		$searchArr['s_policynumber'] = $this->getState('s_policynumber');
		$searchArr['s_firstname'] = $this->getState('s_firstname');
		$searchArr['s_lastname'] = $this->getState('s_lastname');
		$searchArr['s_email'] = $this->getState('s_email');
		$searchArr['s_cemail'] = $this->getState('s_email');
		
		if(!empty($searchArr['s_ordernumber'])){
			$q .= " AND b.id LIKE '%".$searchArr['s_ordernumber']."%' ";
		}
		if(!empty($searchArr['s_policynumber'])){
			$q .= " AND b.policy_number LIKE '%".$searchArr['s_policynumber']."%' ";
		}
		if(!empty($searchArr['s_email'])){
			$did = 'data REGEXP '."'".'.*"email";s:[0-9]+:"[a-z,0-9,@,.]*'.$searchArr['s_email'].'[a-z,0-9,@,.]*".*'."'";
			$q .= " AND $did";
		}
		if(!empty($searchArr['s_firstname'])){
			$did = 'data REGEXP '."'".'.*"firstname";s:[0-9]+:"[a-z,0-9]*'.$searchArr['s_firstname'].'[a-z,0-9]*".*'."'";
			$q .= " AND $did";
		}
		if(!empty($searchArr['s_lastname'])){
			$did = 'data REGEXP '."'".'.*"lastname";s:[0-9]+:"[a-z,0-9]*'.$searchArr['s_lastname'].'[a-z,0-9]*".*'."'";
			$q .= " AND $did";
		}
		if(!empty($searchArr['s_email'])){
			$did = 'data REGEXP '."'".'.*"confirmemail";s:[0-9]+:"[a-z,0-9,@,.]*'.$searchArr['s_email'].'[a-z,0-9,@,.]*".*'."'";
			$q .= " AND $did";
		}

		// echo "SELECT COUNT(*) FROM #__users AS a INNER JOIN #__event_manage_session AS b ON b.user = a.id LEFT JOIN #__user_usergroup_map AS ug ON ug.user_id = a.id WHERE a.block = 0 $q AND ug.group_id NOT IN (6,8)";
		$db->setQuery("SELECT COUNT(*) FROM #__users AS a INNER JOIN #__event_manage_session AS b ON b.user = a.id LEFT JOIN #__user_usergroup_map AS ug ON ug.user_id = a.id WHERE a.block = 0 AND b.status = 1 $q AND ug.group_id NOT IN (6,8)");
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		return $pagination->getListFooter();
	 
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

		$query->where('a.order_id = 0');

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
			if($perfomer == '13+'){
				$perfomer = '9-12';
			}
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
		// echo str_replace('#_', 'fkk', $query);
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

	// this function call for create new policy by users
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

		$newdataArr = $data['data'];
  		foreach ($newdataArr as $page => $pvalue) {
  			foreach ($pvalue as $key => $value) {
  					if(is_array($value)){
  						$nvalue = '';
  						foreach ($value as $ackey => $acvalue) {
  							$value[$ackey] = str_replace("'", "&#039;", $acvalue);
  							$nvalue = $value;
  						}
  						$newArr[$page][$key] = $nvalue;
  					}else{
  						$newArr[$page][$key] = str_replace("'", "&#039;", $value);
  					}
  				// $newArr[$page][$key] = addslashes($value);
  			}
  		}
  		
  		$data['data'] = $newArr;

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

	// this function call for update policy detail by users
	function UpdateEventsAndTerms($data,$sid){
  	
        //$dd = $session_model->getItem($pk);	
        $user = & JFactory::getUser();
        $Uid = $user->get( 'id' );
        $sql = '';

        $newdataArr = $data['data'];
  		foreach ($newdataArr as $page => $pvalue) {
  			foreach ($pvalue as $key => $value) {
  					if(is_array($value)){
  						$nvalue = '';
  						foreach ($value as $ackey => $acvalue) {
  							$value[$ackey] = str_replace("'", "&#039;", $acvalue);
  							$nvalue = $value;
  						}
  						$newArr[$page][$key] = $nvalue;
  					}else{
  						$newArr[$page][$key] = str_replace("'", "&#039;", $value);
  					}
  				// $newArr[$page][$key] = addslashes($value);
  			}
  		}
  		
  		$data['data'] = $newArr;

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
        	$sql .= ' `policy_number` = "'.$data['policy_number'].'",';
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

	// return object class array
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
		$newdataArr = unserialize($result->data);
		foreach ($newdataArr as $page => $pvalue) {
  			foreach ($pvalue as $key => $value) {
  					if(is_array($value)){
  						$nvalue = '';
  						foreach ($value as $ackey => $acvalue) {
  							$value[$ackey] = str_replace("&#039;", "'", $acvalue);
  							// $value[$ackey] = str_replace("&#039;", '"', $acvalue);
  							$nvalue = $value;
  						}
  						$newArr[$page][$key] = $nvalue;
  					}else{
  						$newArr[$page][$key] = str_replace("&#039;", "'", $value);
  					}
  				// $newArr[$page][$key] = addslashes($value);
  			}
  		}
  		
  		// print_r($newArr);
  		$result->data = serialize($newArr);
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
		$newdataArr = unserialize($result->data);
		foreach ($newdataArr as $page => $pvalue) {
  			foreach ($pvalue as $key => $value) {
  					if(is_array($value)){
  						$nvalue = '';
  						foreach ($value as $ackey => $acvalue) {
  							$value[$ackey] = str_replace("&#039;", "'", $acvalue);
  							// $value[$ackey] = str_replace("&#039;", '"', $acvalue);
  							$nvalue = $value;
  						}
  						$newArr[$page][$key] = $nvalue;
  					}else{
  						$newArr[$page][$key] = str_replace("&#039;", "'", $value);
  					}
  				// $newArr[$page][$key] = addslashes($value);
  			}
  		}
  		
  		// print_r($newArr);
  		$result->data = serialize($newArr);
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

		//echo $this->getState('s');
        // Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query = "SELECT DISTINCT a.id ,a.*, b.* FROM #__users AS a INNER JOIN #__event_manage_session AS b ON b.user = a.id LEFT JOIN #__user_usergroup_map AS ug ON ug.user_id = a.id WHERE a.block = 0 AND b.status = 1 AND ug.group_id NOT IN (6,8)"; 

		$searchArr['s_ordernumber'] = $this->getState('s_ordernumber');
		$searchArr['s_policynumber'] = $this->getState('s_policynumber');
		$searchArr['s_firstname'] = $this->getState('s_firstname');
		$searchArr['s_lastname'] = $this->getState('s_lastname');
		$searchArr['s_email'] = $this->getState('s_email');
		$searchArr['s_cemail'] = $this->getState('s_email');

		if(!empty($searchArr['s_ordernumber'])){
			$query .= " AND b.id LIKE '%".$searchArr['s_ordernumber']."%' ";
		}
		if(!empty($searchArr['s_policynumber'])){
			$query .= " AND b.policy_number LIKE '%".$searchArr['s_policynumber']."%' ";
		}
		if(!empty($searchArr['s_email'])){
			$did = 'data REGEXP '."'".'.*"email";s:[0-9]+:"[a-z,0-9,@,.]*'.$searchArr['s_email'].'[a-z,0-9,@,.]*".*'."'";
			$query .= " AND $did";
		}
		if(!empty($searchArr['s_firstname'])){
			$did = 'data REGEXP '."'".'.*"firstname";s:[0-9]+:"[a-z,0-9]*'.$searchArr['s_firstname'].'[a-z,0-9]*".*'."'";
			$query .= " AND $did";
		}
		if(!empty($searchArr['s_lastname'])){
			$did = 'data REGEXP '."'".'.*"lastname";s:[0-9]+:"[a-z,0-9]*'.$searchArr['s_lastname'].'[a-z,0-9]*".*'."'";
			$query .= " AND $did";
		}
		if(!empty($searchArr['s_email'])){
			$did = 'data REGEXP '."'".'.*"confirmemail";s:[0-9]+:"[a-z,0-9,@,.]*'.$searchArr['s_cemail'].'[a-z,0-9,@,.]*".*'."'";
			$query .= " AND $did";
		}

		//data REGEXP '.*"page4[`firstname`]";s:[0-9]+:"Nikhil".*'

		$query .= " ORDER BY b.id DESC";
		$search = 0;
		foreach ($searchArr as $key => $value) {
				if($value != ""){
					$search = 1;
				}
		}
		
		if(empty($search)){
			$query .= " LIMIT " . $this->getState('limitstart') . ", " . $this->getState('limit');
		}

		// echo str_replace('#_', 'fkk', $query);
		
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
                $emailBody .= "<div align='left'><a href='https://www.entertainmentinsurance.net.au/'><img src='".JURI::base()."/images/new_logo.png' alt='Action Entertainment Insurance' title='Action Entertainment Insurance' width='598' height='186' /></a></div><br/><br/>";
                $emailBody .= 'Dear '.$data['name'].'<br /><br />';
                $emailBody .= 'Thanks for using our Online Quotation Platform.<br><br>Your new account has been created, please keep the login details below in a safe place<br><br>';
		$emailBody .= 'You can access your details at the URL: '.$data['siteurl'].'<br>';
                $emailBody .= 'Account Name: '.$data['name'].'<br>';
		$emailBody .= 'Username: '.$data['username'].'<br>';
		$emailBody .= 'Password: '.$password;

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
			$mailer->setSubject('New Account Details – Action Entertainment Insurance');
			$mailer->isHTML(true);
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
		
	}

	function getDataByIdForDashborad($userid){
		
      // Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query = "SELECT a.*, b.* FROM #__users AS a INNER JOIN #__event_manage_session AS b ON b.user = a.id LEFT JOIN #__user_usergroup_map AS ug ON ug.user_id = a.id WHERE a.block = 0 AND a.id = $userid AND b.status = 1 AND ug.group_id IN (2) ORDER BY b.activity_status ";
		//echo $query;
		$db->setQuery($query);
		$result = $db->loadObjectlist();
	
		return $result;                  
	
	}

	function updateUnpaidToPaid($pid){

		$db = JFactory::getDBO();
		$query = "UPDATE `#__event_manage_session` SET `activity_status`= 'Active', `sp_status`= 'manualy'  WHERE `id` = '".$pid."'";
		$db->setQuery($query);
		$checkId = $db->execute();		
		if($checkId){
			
			$pno = 1000+$pid;
			$policyno = $this->createPolicyNumber($pno);
			$query = "UPDATE `#__event_manage_session` SET `policy_number`= '".$policyno."' WHERE `id` = '".$pid."'";
			$db->setQuery($query);
			$result = $db->execute();		
			
			return true;
		}else{
			return false;
		}	                           
	
	}

	function addExtraFieldWithOrderId($data){
		
		
		$db = JFactory::getDBO();

		$db->setQuery('SELECT MAX(ordering) FROM #__event_manage_term');
		$max = $db->loadResult();
		$ordering = $max + 1;


		if($data['onpage'] == 1){
			$fetchOrderDetail = $this->getSessionDataById($data['order_id']);
			$ReffralPageArr = unserialize($fetchOrderDetail->data);
			if($ReffralPageArr['page1']['perfomers'] == '13+' && $ReffralPageArr['page1']['annualincome'] == '$250,000+'){
				$query = "SELECT COUNT(*) FROM #__event_manage_term WHERE activity = 'Excharge' AND order_id = '".$data['order_id']."'";
				$query .= " AND (name LIKE '%premium%' OR name LIKE '%brokerfee%')";
				// echo $query;
				$db->setQuery($query);
				$ExtraFieldExits = $db->loadResult();
				if(!$ExtraFieldExits){
					$data['numberofreffral'] = 2;
				}
			}
		}	

		 $query = "INSERT INTO `#__event_manage_term` SET 
				 `ordering`= '".$ordering."',
				 `state`= '".$data['state']."',
				 `created_by`= '".$data['created_by']."',
				 `name`= '".$data['name']."',
				 `price`= '".$data['price']."',
				 `create_date`= '".$data['create_date']."',
				 `description`= '".$data['description']."',
				 `activity`= '".$data['activity']."',
				 `order_id`= '".$data['order_id']."',
				 `referral_page_no`= '".$data['onpage']."'";
		
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";

		// return true;
		$db->setQuery($query);
		$result = $db->execute();

		if($result && empty($data['numberofreffral'])){

			if($data['activity_status'] == 'Referral'){
				$activity_status = 'Unfinished';
			}else if($data['activity_status'] == 'Hold'){
				$activity_status = 'Unpaid';
			}else{
				$activity_status = $data['activity_status'];
			}

			$query = "UPDATE `#__event_manage_session` SET `activity_status`= '".$activity_status."'  WHERE `id` = '".$data['order_id']."'";
			
			$db->setQuery($query);
			$checkId = $db->execute();		
		}
		return  $result;                        
	
	}

	function getNumberOfExtraFieldOrderWise($orderid,$onpage,$reffral = null){

		$db = JFactory::getDBO();
		
		$query = "SELECT COUNT(*) FROM #__event_manage_term WHERE order_id = '".$orderid."' AND referral_page_no = ".$onpage;
		
		if(!empty($reffral) && $reffral == '13+'){
		 $query .= " AND name IN ('premium','brokerfee')";
		}
		// echo $query;
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	
	}

	function getExtraFieldOrderWise($orderid,$reffral = null){

		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__event_manage_term WHERE activity = 'Excharge' AND order_id =".$orderid ;
		if(!empty($reffral) && $reffral == '13+'){
		$query .= " AND name IN ('premium','brokerfee')";
		}
		
		// echo $query;

		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	
	}

	function getExtraFieldByOrder($orderid){

		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__event_manage_term WHERE activity = 'Excharge' AND order_id =".$orderid ;
		$query .= " AND name NOT IN ('premium','brokerfee')";
		
		// echo $query;
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	
	}


	function getExtraFieldForPremiumByOrder($orderid){

		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__event_manage_term WHERE activity = 'Excharge' AND order_id =".$orderid);
		$result = $db->loadObjectList();
		return $result;
	
	}

	// this function call for create user unique policy number 
	function createPolicyNumber($val){
		$prefix = 'PS';
		$digit = '';
		$val1 = strlen($val);
		if($val1 == 4){
			$digit = '0';
		}

		return $prefix.$digit.$val ;

	}

	// this function call for update user status
	function changeOrderStatus($oid,$status,$cancelpolicy = null){

		if(!empty($cancelpolicy)){ $query = ", `policy_cancel` = '".$cancelpolicy."'"; } else { $query = ''; } // Change policy_cancel status when user cancel policy
		if($status == 'Y'){ $status = 1; } else if($status == 'N'){ $status = 0; } // Change policy status when user cancel or delete policy

		$db = JFactory::getDBO();
		$query = "UPDATE `#__event_manage_session` SET `status`= '".$status."' $query WHERE `id` = '".$oid."'";
		$db->setQuery($query);
		$result = $db->execute();
		return $result;	
	
	}

	// this function call for get policy-number by order-id
	function getPolicyNumberByOrderId($orderid){

		$db = JFactory::getDBO();
		$db->setQuery("SELECT policy_number,status,policy_cancel FROM #__event_manage_session WHERE activity_status = 'Active' AND id =".$orderid);
		$result = $db->loadObject();
		return $result;
	
	}

	// this function call for get activity_status by order-id
	function getActivityStatusByOrderId($orderid){

		$db = JFactory::getDBO();
		$query = "SELECT activity_status FROM #__event_manage_session WHERE id =".$orderid;
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	
	}

	function gernatePDF($userdata){

		/**
		 * Creates an example PDF TEST document using TCPDF
		 * @package com.tecnick.tcpdf
		 * @abstract TCPDF - Example: Default Header and Footer
		 * @author Nicola Asuni
		 * @since 2008-03-04
		 */
		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->chargecurrency = $componentParams->get('chargecurrency');
		$this->insurername = $componentParams->get('insurername');
		$this->insureraddress = $componentParams->get('insureraddress');
		$this->abn = $componentParams->get('abn');
		
		// $data = unserialize($userdata->data);
		$newdataArr = unserialize($userdata->data);
		foreach ($newdataArr as $page => $pvalue) {
  			foreach ($pvalue as $key => $value) {
  					if(is_array($value)){
  						$nvalue = '';
  						foreach ($value as $ackey => $acvalue) {
  							$value[$ackey] = str_replace("&#039;", "'", $acvalue);
  							// $value[$ackey] = str_replace("&#039;", '"', $acvalue);
  							$nvalue = $value;
  						}
  						$newArr[$page][$key] = $nvalue;
  					}else{
  						$newArr[$page][$key] = str_replace("&#039;", "'", $value);
  					}
  				// $newArr[$page][$key] = addslashes($value);
  			}
  		}
  		
  		$data = $newArr;
		// echo "<pre>";
		// print_r($userdata);
		// print_r($data);
		// echo "</pre>";
		$class_of_policy = "Perform-Sure Liability Insurance";
		$policy_no = $userdata->policy_number;
		$invoice_no = "290854";
		$invoice_date = date("d/m/Y");
		$reference = "ROBINSONKA";
		$insured = $data['page4']['insuredname'];
		$addressline1 = $data['page4']['streetaddress1'];
		$addressline2 = $data['page4']['streetaddress2'];
		$suburb = $data['page4']['suburb'];
		$this->statename = $this->getStateName();
		$state = '';
		foreach ($this->statename as $key => $value) {
			if($value->id == $data['page4']['state']){
				$state =  $value->name;
			}	
		}

		$postcode = $data['page4']['postcode'];
		$startdate = date("d/m/Y",strtotime($data['page1']['start_date']));
		$enddate = date("d/m/Y",strtotime('+1 year',strtotime($data['page1']['start_date'])));
		if(!empty($data['page2']['premium'])){
			$premium = '$'.$data['page2']['premium'];
		}else{
			$premium = '$0.00';
		}
		
		$totalgst = round($data['page2']['gstpremium']+$data['page2']['gstfee'],2); 
		if(!empty($totalgst)){
			$gst = '$'.$totalgst;
		}else{
			$gst = '$0.00';
		}

		if(!empty($data['page2']['stampduty'])){
			$stampduty = '$'.$data['page2']['stampduty'];
		}else{ 
			$stampduty = '$0.00'; 
		}

		if(!empty($data['page2']['brokerfee'])){
			$brokerfee = '$'.$data['page2']['brokerfee'];	
		}else{
			$brokerfee = '$0.00';	
		}
		/* get value from Backend */
		$currency = $this->chargecurrency;
		$insurername = $this->insurername;
		$insureraddress = $this->insureraddress;
		$abn = $this->abn;
		/* End Backend */

		$tp = number_format((float)$data['page6']['subtotal'], 2, '.', '');
		if(!empty($tp)){
			$totalpremium = '$'.$tp;
		}else{
			$totalpremium = '$0.00';
		}

		$totalcommission = round($data['page2']['commission']+$data['page2']['gstcommission'],2); 
		if(!empty($totalcommission)){
			$commission = '$'.$totalcommission;	
		}else{
			$commission = '$0.00';
		}
		
		$creditcardfee = '$'.round($data['page6']['subtotal']*1.5/100,2);
		$activity = '';
		foreach ($data['page1']['activity'] as $avalue) {
			$activity .= $avalue.',';
		}
		$activity = substr($activity,0,-1);
		$this->perfomers = $this->getTerm('Perfomers');
		foreach ($this->perfomers as $pvalue) {
			if($data['page1']['perfomers'] == $pvalue->price){
				$perf =  $pvalue->name;
			}
		}
		/* OLD */
		//$performers = $perf;
		/* New */
		$performers = $data['page1']['perfomers'];

		if(!empty($data['page6']['publicliailitycover']) && (strpos($data['page6']['publicliailitycover'],'$') !== False)){
			$publicliability = $data['page6']['publicliailitycover'];
		}else if(!empty($data['page6']['publicliailitycover'])){
			$publicliability = number_format($data['page6']['publicliailitycover']);
		}else{
			$publicliability = '$0.00';
		}

		$this->Extracharegs = $this->getTerm('Excharge');
		$this->ExtraCharegsByOrderId = $this->getExtraFieldByOrder($userdata->id);
		$charges = 0;
		if(!empty($this->Extracharegs)){
			$Extrachreges = $this->Extracharegs;
			foreach ($Extrachreges as $evalue) {
				$charges += $evalue->price;
			}
		}

		if(!empty($this->ExtraCharegsByOrderId)){
			$ChregesByOrderId = $this->ExtraCharegsByOrderId;
			foreach ($ChregesByOrderId as $cvalue) {
				$charges += $cvalue->price;
			}
		}
		if(!empty($charges)){
			$TotalExtras = '$'.round($charges,2);	
		}else{
			$TotalExtras = '$0.00';	
		}
		$gernatedate = date('dmYHis');
		// $filename = $policy_no.'_'.$gernatedate.'.pdf';
		$filename = 'Invoice - Performsure Policy '.$policy_no.'.pdf';
		// require_once('tcpdf/tcpdf.php');
		require_once(JPATH_ROOT.'/components/pdf/tcpdf_extend.php');

		// create new PDF document
		$pdf = new TCPDF_EXTEND(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

		$pdf->class_of_policy = "Perform-Sure Liability Insurance";
		$pdf->insurername = $insurername;
		$pdf->insureraddress = $insureraddress;
		$pdf->abn = $abn;
		$pdf->policy_no = $policy_no;
		$pdf->invoice_date = $invoice_date;
		$pdf->invoice_no = $invoice_no;
		$pdf->reference = $reference;
		$pdf->insured = $insured;
		$pdf->addressline1 = $addressline1;
		$pdf->addressline2 = $addressline2;
		$pdf->suburb = $suburb;
		$pdf->states = $state;
		$pdf->postcode = $postcode;
		$pdf->startdate = $startdate;
		$pdf->enddate = $enddate;
		$pdf->premium = $premium;
		$pdf->gst = $gst;
		$pdf->stampduty = $stampduty;
		$pdf->brokerfee = $brokerfee;
		$pdf->currency = $currency;
		$pdf->totalpremium = $totalpremium;
		$pdf->commission = $commission;
		$pdf->creditcardfee = $creditcardfee;
		$pdf->activity = $activity;
		$pdf->performers = $performers;
		$pdf->publicliability = $publicliability;
		$pdf->totalextras = $TotalExtras;

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Insurance');
		$pdf->SetTitle('Insurance Test PDF');
		$pdf->SetSubject('Test Subject');
		$pdf->SetKeywords('Insurance');

		$pdf->setPrintFooter(true);

		// set default monospaced font
		// $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetMargins(10, 10, 10);
		$pdf->SetHeaderMargin(10);
		$pdf->SetFooterMargin(10);

		$pdf->SetAutoPageBreak(TRUE, 5);

		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->setPageOrientation('P',TRUE,10);

		// set some language-dependent strings (optional)
		// if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		// 	require_once(dirname(__FILE__).'/lang/eng.php');
		// 	$pdf->setLanguageArray($l);
		// }

		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		$pdf->SetFont('times', '', 11, '', true);


$pdf->AddPage();
$first_page = <<<EOD
	<table width="100%">
		<tr>
			<td width="100%" style="text-align: center;"><img src="components/pdf/new_logo.png" /></td>
		</tr>
		<tr>
			<td width="100%" style="text-align: center;">
				<span style="font-size: 14px;"><b>ACTION ENTERTAINMENT INSURANCE PTY LTD</b></span><br/>
				Corporate Representative No. 237473 as an authorised representative of Action Insurance Brokers P/L<br/>
				ABN $pdf->abn AFS 225047<br/>
				Suite 301, Building A, "Sky City", 20 Lexington Drive Bella Vista NSW 2153<br/>
				Phone: 1300 655 424 Fax: (02) 8935 1501<br/>
				Website: <a href="http://www.entertainmentinsurance.net.au">www.entertainmentinsurance.net.au</a><br/> Email: <a href="mailto:entertainment@actioninsurance.com.au">entertainment@actioninsurance.com.au</a><br/>
				<br/>
			</td>
		</tr>
	</table>
	<br/>
	<table width="100%" cellpadding="10">
		<tr>
			<td width="65%">
				<p style="font-size: 12px; padding: 5px;"><i>As per your request, we have arranged the following insurance policy with effect from the $pdf->startdate.</i></p>
				<div>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$pdf->insured<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$pdf->addressline1<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$pdf->addressline2<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$pdf->suburb $pdf->states $pdf->postcode<br/>
				</div>
			</td>
			<td width="35%">
				<div style="text-align: center; border: 1px solid #000; border-radius: 5px;">
					<span style="font-size: 18px;"><b>TAX INVOICE</b></span><br/>
					<span style="font-size: 10px; line-height:0;">This document will be a tax invoice <br/> for GST when you make payment</span><br/>
				</div>
				<table style="font-size: 10px;">
					<tr>
						<td><b>Invoice Date:</b></td>
						<td>$pdf->invoice_date</td>
					</tr>
					<tr>
						<td><b>Policy Number:</b></td>
						<td>$pdf->policy_no</td>
					</tr>
				</table>
				<br/><br/>
				<span style="font-size: 9px;">Should you have any queries in relation to this account,please contact your Account Manager<br/>
					Action Entertainment Online
				</span>
			</td>
		</tr>
	</table>
	<br/>
	<table width="100%" cellpadding="10">
		<tr>
			<td width="65%">
				<div style="border: 1px solid #000; border-radius: 5px;">
					<table width="100%">
						<tr>
							<td><b> NEW POLICY</b></td>
						</tr>
						<tr>
							<td><b> Class of Policy:</b>Perform-Sure Liability Insurance</td>
						</tr>
						<tr>
							<td><b> Insurer:</b>$pdf->insurername<br/>$pdf->insureraddress</td>
						</tr>
						<tr>
							<td>ABN: $pdf->abn &nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><b> The Insured:</b>&nbsp;&nbsp;$pdf->insured</td>
						</tr>
						<tr>
							<td><b> Policy No:</b>&nbsp;&nbsp;$pdf->policy_no</td>
						</tr>
						<tr style="line-height: 1;">
							<td><b> Period of Cover:</b>&nbsp;&nbsp;From <b>$pdf->startdate</b> to <b>$pdf->enddate</b> at 4:00 pm</td>
						</tr>
					</table>
				</div>
				<div>
					<b>Details: </b>
					<span style="font-size: 10px;">See attached schedule for a description of the risk(s) insured</span>
				</div>
			</td>
			<td width="35%">
				<p style="font-size: 9px;">
					<b>YOUR DUTY OF DISCLOSURE</b><br/>
					Before you enter into a Contract of general insurance with an Insurer, you have a duty under the Insurance Contracts Act 1984 to disclose to the Insurer every matter that you know, or could reasonably expect to know, is relevant to the Insurer's decision whether to accept the risk of Insurance and if so, on what terms. You have the same duty to disclose those matters to the Insurer before you renew, extend, vary or reinstate a Contract of general insurance. Your duty however does not require disclosure of matter.<br/>
						- that diminishes the risk to be undertaken by the Insurer<br/>
						- that is common knowledge<br/>
						- that your Insurer knows or, in the ordinary course of	business, ought to know<br/>
						- as to which the compliance with your duty is waived by the Insurer.
				</p>
				<p style="font-size: 9px;">
					<b>NON-DISCLOSURE</b><br/>
					If you fail to comply with your duty of disclosure, the Insurer may be entitled to reduce the liability under the Contract in respect of a claim or may cancel the Contract. If your non-disclosure is fraudulent, the Insurer may also have the option of avoiding the Contract from its beginning.
				</p>
			</td>
		</tr>
	</table>
	<br/>
	<table width="100%" cellpadding="10">
		<tr>
			<td width="65%">
				<span><b>Your Premium:</b></span>
				<div style="border: 1px solid #000; border-radius: 5px;">
					<table width="100%" style="font-size: 10px;">
						<tr>
							<td align="right" width="16%"><b>Premium</b></td>
							<td align="right" width="12%"><b>UW Levy</b></td>
							<td align="right" width="12%"><b>Fire Levy</b></td>
							<td align="right" width="12%"><b>GST</b></td>
							<td align="right" width="16%"><b>Stamp Duty</b></td>
							<td align="right" width="16%"><b>Broker Fee</b></td>
							<td align="right" width="16%"><b>Extra Charge</b></td>
						</tr>
						<tr>
							<td align="right" width="16%"><b>$pdf->premium</b></td>
							<td align="right" width="12%"><b>$0.00</b></td>
							<td align="right" width="12%"><b>$0.00</b></td>
							<td align="right" width="12%"><b>$pdf->gst</b></td>
							<td align="right" width="16%"><b>$pdf->stampduty</b></td>
							<td align="right" width="16%"><b>$pdf->brokerfee</b></td>
							<td align="right" width="16%"><b>$pdf->totalextras</b></td>
						</tr>
					</table>
				</div>
				<table width="100%">
					<tr>
						<td width="50%" style="font-size: 10px;">Commission we have earned on this invoice inc. GST $pdf->commission</td>
						<td width="50%">
							<div style="border: 1px solid #000;">
								<table>
									<tr>
										<td width="33%"><b>TOTAL</b></td>
										<td width="33%"><b>$pdf->currency</b></td>
										<td width="33%"><b>$pdf->totalpremium</b></td>
									</tr>
									<tr>
										<td colspan="2" width="66%" style="font-size: 8px;">(Excluding Credit Card fee)</td>
										<td width="33%"></td>
									</tr>
								</table>
							</div>
							<table width="100%" style="font-size: 10px;">
								<tr>
									<td width="66%">Credit Card fee (inc GST) is </td>
									<td width="33%" align="right">$pdf->creditcardfee&nbsp;&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td width="35%">
				<br/><br/>
				<p style="font-size: 9px;">
					<b>Action Entertainment Insurance Pty Ltd</b><br/>
					is a member of the Financial Ombudmans Service (FOS), a free client service for disputes. For more information or to lodge a complaint, contact the FOS on 1800 367 287
				</p>
			</td>
		</tr>
	</table>
EOD;
$pdf->writeHTMLCell(0, 0, '', '', $first_page, 0, 1, 0, true, '', true);


$pdf->SetMargins(20, 35, 20);


$pdf->AddPage();
$sub_pages = <<<EOD
	<div>
		This policy has been placed with<br/><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Arena Underwriting (Entertainment - Berkley)<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Level 1, 102 Tudor Street, Hamilton NSW 2303<br/><br/>
		Arena Underwriting (Entertainment - Berkley) is underwritten by<br/><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$pdf->insurername<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ABN $pdf->abn<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$pdf->insureraddress<br/>
		<br/>
		<b><u>PERFORMSURE LIABILITY INSURANCE</u></b><br/><br/>
		<b>PUBLIC/PRODUCTS LIABILITY INSURANCE FOR PERFORMERS/ENTERTAINERS</b><br/><br/>
		<b><u>OCCUPATION:</u></b><br/><br/>
		<b>Principally:</b> &nbsp;&nbsp;<span style="color: black;">$pdf->activity</span><br/><br/>
		<b>Number of Performers/Entertainers Declared:</b><span style="color: black;"> &nbsp;&nbsp;# $pdf->performers</span><br/><br/>
		<b><u>ENDORSEMENTS</u></b><br/><br/>
		It is hereby declared and agreed that:-
		<ul>
			<li>Exclusion 4.13 Crowd Control, is deleted from the Policy and will have no effect.</li>
			<li>Exclusion 4.16 Crowd Surfing, Moshing And Stage Diving, is deleted from the Policy and will have no effect.</li>
		</ul><br/>
		<b>CONTRACTOR / SUBCONTRACTOR INDEMNITY EXTENSION</b>
		<p>For the purposes of this Policy the definition the <b>Insured</b> shall include <b>any contracted or substitute performer, teacher, facilitator or crew member</b>, whilst engaged by the Insured to perform work on the Insured's behalf.</p>
		<p>Public and Products Liability will apply to each person as if a Policy has been separately issued to each contracted or substitute performer, teacher, facilitator or crew member. However, nothing in this endorsement extends or increases the Limit of Liability under the Policy.</p>
		<p>The Insurer agrees to waive rights of subrogation against any contracted or substitute performer, teacher, facilitator or crew member, to the extent that they are insured under this endorsement.</p>
		<p><b>SPECIAL EXCLUSIONS</b><br/>
		<i>Please take note of the following special exclusions which apply to this policy. There are other exclusions which apply and these can be found in the policy wording.</i></p>
		<br/><br/><br/><br/><p><b>Exclusion 4.14 Pyrotechnics</b><br/>
		 This Policy does not cover liability in respect of Personal Injury or Property Damage arising out of or caused by or in connection with the storage and/or use of any pyrotechnics. However, this exclusion shall not apply to the Insured's vicarious liability arising from an act, error or omission of contracted pyrotechnicians that are appropriately licensed & have provided the Insured with proof of liability insurance.</p>
		<b>Exclusion 4.15 Participation Risk</b>
		<br/>The Insurer shall not provide indemnity under this policy for any liability arising out of anyone's participation in any:<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.15.1 &nbsp;&nbsp;sport, game, match, race, practice, training course, trial, contest, competition; or<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.15.2 &nbsp;&nbsp;performances involving the use of fire.<br/>
		<br/>This exclusion does not apply to the following:<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(a) &nbsp;&nbsp;party games played by children which are put on by children's entertainers;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(b) &nbsp;&nbsp;practices which involve rehearsal or practicing for performing arts performances;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(c) &nbsp;&nbsp;competitions/contests that involve non-contact sports or non contact physical challenges.<br/>
		<br/><b>Exclusion 4.18 Self-Promoted Show, Performance or Concert</b><br/>
		<br/>This Policy does not cover liability in respect of Personal Injury or Property Damage arising from shows, performances or concerts where the Insured is acting as event organiser, event promoter or who "self-promote" their own performances "Self-promoted" means Insureds who hire out venues to stage their own shows, performances or concerts. Door-deals are not considered "self-promoted". This Exclusion DOES NOT apply to Insureds who are running workshops and are required to hire a venue or premises in order to conduct these workshops.<br/>
		<br/><b>Exclusion 4.19 Workshop/Tuition - Excluded Activities</b><br/>
		<br/>This Policy does not cover liability in respect of Personal Injury or Property Damage arising from Insureds' who are responsible for staging workshops or activities which include any of the following:-<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.1 &nbsp;&nbsp;Dance schools where this is their primary business;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.2 &nbsp;&nbsp;Drama schools where this is their primary business;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.3 &nbsp;&nbsp;Tuition of or participation in aerial, acrobatics or trapeze activities;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.4 &nbsp;&nbsp;Tuition of or participation in fire performing (but only where naked flames are being used<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.5 &nbsp;&nbsp;Tuition of or participation in gymnastics;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.6 &nbsp;&nbsp;Tuition of or participation in competitive sporting activities;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.7 &nbsp;&nbsp;Tuition of or participation in adventure type activities;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.8 &nbsp;&nbsp;Tuition of or participation in circus skills;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.9 &nbsp;&nbsp;Tuition of or participation in Tattooing and body piercing (face painting and spray on tattooing are covered);<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.19.10 &nbsp;&nbsp;Tuition of or participation in Filming and/or video production activities.<br/>
		<br/><b><u>SITUATION:</u></b><br/>
		<br/>At and from &nbsp;&nbsp;&nbsp;&nbsp;<span style="color: red;">$pdf->suburb, $pdf->states</span><br/>
		<br/><br/><b><u>GEOGRAPHICAL SCOPE:</u></b><br/>
		<br/>WORLD WIDE EXCLUDING NORTH AMERICA<br/>
		<p><b><u>LIMITS OF INSURANCE:</u></b></p>
		<table width="100%">
			<tr>
				<td width="45%">Public Liability</td>
				<td width="40%">Each & Every Occurrence </td>
				<td width="15%">$ $pdf->publicliability</td>
			</tr>
			<tr>
				<td width="45%">Products Liability</td>
				<td width="40%">Each Policy Period (Aggregate Limit) </td>
				<td width="15%">$ 20,000,000</td>
			</tr>
			<tr>
				<td width="45%">Goods in Physical or Legal Control Sub-limit</td>
				<td width="40%">Each Policy Period (Aggregate Limit) </td>
				<td width="15%">$ 250,000</td>
			</tr>
		</table>
		<p><b><u>DEDUCTIBLE (EXCESS)</u></b></p>
		<table width="100%" style="font-size:13px;">
			<tr>
				<td width="40%">Public Liability Claims</td>
				<td width="50%">Each & Every Occurrence (defence costs inclusive) </td>
				<td width="10%">$ 500</td>
			</tr>
			<tr>
				<td width="40%">Products Liability Claims</td>
				<td width="50%">Each & Every Occurrence (defence costs inclusive) </td>
				<td width="10%">$ 500</td>
			</tr>
			<tr>
				<td width="40%">Goods in Physical or Legal Control Claims</td>
				<td width="50%">Each & Every Occurrence (defence costs inclusive) </td>
				<td width="10%">$ 500</td>
			</tr>
		</table>
		<br/><br/><b><u>LIABILITY ASSUMED UNDER CONTRACT</u></b>
		<p>This insurance does not cover claims arising from liability assumed under contract. Contractual liability is where you have assumed someone else's liability risks under a contractual arrangement. They are commonly known as 'hold harmless' or 'indemnity agreements'. Such agreements can be found in venue hire contracts, equipment or property hire contracts, and agreements with Government Authorities and Organisations.<br/>
		   We recommend that you do not sign these agreements without first referring them to our office.<br/>
		   If you are required to sign such an agreement, please provide a copy of this to Action Insurance Brokers Pty Limited so that we can refer it to your Insurer.</p>
		<p><b><u>DISCLAIMER</u></b></p>
		<p>This schedule is only a summary of cover. Please refer to the policy wording for full details of the scope of cover including the terms, conditions and exclusions which apply to the insurance.</p>
	</div>
EOD;
$pdf->writeHTMLCell(0, 0, '', '', $sub_pages, 0, 1, 0, true, '', true);


$pdf->AddPage();
$sub_pages = <<<EOD
	<p style="text-align: center;"><b>Your Insurance Advisor's Comments in relation to this Policy<br/>Version 19/06/2014</b></p>
	<p>This information is provided in accordance with the Financial Services Reform Act 2001 (Cth) and other relevant legislation. It includes information that will assist you in determining if this insurance is right for you and further, if you should consider other insurance policies to meet your risk management needs. It is essential you read this document completely and contact your Action Insurance Brokers advisor if you have any questions relating to your policy or the information contained herein.</p>
	<p>Reference in this document to You / Your / Insured / Insured Party / Insured parties means the parties listed on this policy as the insured parties and extends to include any directors, trustees, business owners and business partners, owners of insured property, spouses, defacto partners and legal guardians.</p>
	<p>Reference in this document to We / Our / Action means Action Insurance Brokers P/L, its directors, advisors and employees.</p>
	<p><b>Version BA1 - Binder Advice Warning</b>In arranging this insurance policy for you, it is important that you are made aware of the following information: -</p>
	<p>Action Insurance Brokers P/L (AFS# 225047) is a licensed insurance broker. Whilst in most cases, we act as an agent of you, our client, we have not done so in this instance.</p>
	<p>This policy has been arranged under a "Binder" between Action Insurance Brokers and the Insurance Company. A binder is an arrangement where we, the broker, act as an agent of the insurance company and not the client. We have such arrangements to obtain the best possible cover and price on a specific type of insurance policy.</p>
	<p>Whilst we believe our offer to arrange this insurance policy is well based, any recommendation we give you does not take into account your personal or business or specific needs or financial situation.</p>
	<p style="text-align: justify;"><b>Cooling off period:</b><br/>Under the Law, you may have the right to return this policy and receive a full refund of premium and charges (except where Govt regulations prohibit the return of charges). You must exercise this right within 14 days of receiving confirmation of cover and this must be done in writing or electronically. If you have lodged a claim during that period, you cannot exercise the right to return it. If the cover was a short term cover, (for less than 14 days), you cannot exercise the right to return it.</p>
	<p style="text-align: justify;"><b>Goods & Services Tax (GST):</b><br/>If you are a business registered for GST purposes, you may be entitled to claim an input tax credit in respect of the GST on the total amount of premium payable for this insurance policy. However, if you do claim such an input tax credit, claim payments made under the policy after 1 July 2000 may, under the GST legislation, become taxable, and you will be obliged to account for the GST payable to the Australian Taxation Office. You will therefore need to consider carefully whether or not to claim an input tax credit in respect of the GST on your premium. We strongly recommend that you seek advice from your accountant or tax adviser on whether it is in your interests to do so. An insured is not entitled to claim an input tax credit before 1st July 2003 unless an election, in a format laid down in the legislation, has been made to the Commissioner of Taxation. If you do claim an input tax credit, you will also need to consult with us over the implications of that decision on the amount you will need to insure for under your policy.</p>
	<p style="text-align: justify;"><b>Product Disclosure Statements, Policy Wordings and Policy Schedules - Issued by your Insurance company:</b><br/>Your Insurance policy is a legally binding contract between You and the Insurance Company. It explains the full terms and conditions of cover. Most policies will have a Policy Schedule with your specific details listed and will also have a standard policy wording and Product Disclosure Statement All three documents must be read in conjunction with each other to fully understand the contract of insurance. Every time you arrange a new policy, endorse or change an existing policy or renew a policy, a new contract of insurance is created and the policy schedule, Product Disclosure Statement and policy wording may change &#45; which might affect your cover and your ability to lodge an insurance claim. Always read the Product Disclosure Statement and Policy wording with your Tax Invoice to ensure you understand your cover.</p>
	<p style="text-align: justify;"><b>Your Duty of Disclosure:</b><br/>Before you enter into a contract of insurance or renew a contract of insurance with an Insurer, you have a duty under the Insurance Contracts Act (1984) to disclose to that insurer, all information which you believe is relevant to that Insurer's decision whether to accept the risk of insurance, and if so, on what terms. You have the same duty to the Insurer when you alter, endorse, renew or change a contract of insurance. Your duty, however, does not require disclosure of matters:<br/>- that diminish the risk to be insured; - that are of common knowledge; - that the Insurer knows, or ought to know in the course of their ordinary business; - as to which compliance with your duty is waived by the Insurer You are also required to tell the insurer if any of the following has occurred in the last 12 months:<br/>- a insurer has refused, cancelled, or imposed a non standard excess on any of your other insurance policies or required you to accept special or unusual terms on other policies; - you or any person benefiting under this policy has been charged or convicted of a criminal offence; - the risk covered under this policy has changed since it was originally insured, to the extent that now may be an increased risk for the insurer. If you fail to meet your duty of disclosure, your insurer may deny, reduce or refuse your claim.</p>
	<b>You can minimise the risk of damage in advance of an emergency by:</b>
	<ul>
		<li>
			Training employees in fire safety, particularly those responsible for storage areas, housekeeping, maintenance and operations where open flames or flammable substances are used.
		</li>
		<li>
			Testing and if necessary modernising the electrical system since faulty wiring causes a large percentage of non-residential fires.
		</li>
		<li>
			Ensuring computer monitors and television screens are not left on stand by mode when not in use. It is estimated that there are around 700 fires a year from this source.
		</li>
		<li>
			Stopping the overloading of power boards and where possible eliminating them altogether. Where they are required purchase good quality ones and have them tested on a regular basis.
		</li>
		<li>
			Ban smoking within all buildings (in line with occupation and health regulations)
		</li>
		<li>
			Carefully control any hot works &#45; grinding, welding, cutting etc.
		</li>
		<li>
			Situating your business in a fire-resistant building - a structure made of non-combustible materials with firewalls that create barriers to the spread of fires - and in a building with a fire alarm system connected to the local fire department. It is also a good idea to have a sprinkler system to douse fires.
		</li>
		<li>
			Limiting storm-related damage by making sure the building conforms to damage-resistant building codes.
		</li>
	</ul>
	<b>You can develop a business continuity plan by:</b>
	<ul>
		<li>Keeping up-to-date triplicate records of both electronic and written records. In some jurisdictions, if companies fail to maintain and safeguard accurate business records, the company may still be held liable.</li>
		<li>Identifying the critical business activities and the resources needed to support them in order to maintain customer service while your business is closed for repairs.</li>
		<li>Planning for the worst possible scenario. Do research before a disaster strikes by finding alternative facilities, equipment and supplies, and locating qualified contractors to repair your facility.</li>
		<li>Setting up an emergency response plan and training employees how to execute it.</li>
		<li>Considering the resources you may need to activate during an emergency such as back-up sources of power and communications systems. Also, stockpiling the supplies you may need such as first-aid kits, and flashlights.</li>
		<li>Compiling a list of important phone numbers (including cell phone numbers) and addresses, including your Action Insurance Brokers office, local and state emergency management agencies, major clients, contractors, suppliers, realtors, financial institutions. The list should also include employees and company officials. Keep copies off the premises in case the disaster is widespread. Ideally a copy should be kept off site.</li>
		<li>Deciding on a communications strategy to prevent loss of your customers. Clients must know how to contact your company at its new location. Among the possibilities to explore, depending on the circumstances, are posting notices outside the original premises; contacting clients by phone, e-mail or regular mail; placing a notice or advertisement in local newspapers; and asking friends and acquaintances in the local business community to help disseminate the information.</li>
		<li>Review and exercise your plan on a regular basis and communicate changes to key employees.</li>
	</ul>
	<p style="text-align: justify;">Use your Business Continuity Plan including the risk assessment, business impact analysis and business continuity plan to set your indemnity period, level of wages cover, and additional increase cost of working. It also assists in determining what extensions to the standard cover are required. Your Action Insurance Brokers adviser should be able to assist you here.</p>
	<p style="text-align: justify;"><b>Workers Compensation Insurance:</b><br/>Workers Compensation insurance, as defined by the various State and Territory legislations, is designed to cover your employees. Where you have employees, you must by law, take out a Workers Compensation Insurance policy. Workers Compensation policies are normally separate insurance policies that are not covered under other policies such as this one. However, if this insurance is to cover Home Building &/or Contents or Residential Strata Insurance it might include cover for Domestic Workers Compensation for your domestic employees such as cleaners, gardeners and the like. If it does, this will be specifically shown in this document along with Building / Contents & other sums insured</p>
	<p style="text-align: justify;"><b>Public & Products Liability Insurance, Professional Indemnity Insurance and Management Liability Insurance including Directors and officers Liability Insurance and how these are affected by Contracts you may have signed:</b><br/>We wish to bring your attention to the importance and unusual aspects of contractual liability agreements entered into by some businesses. Many contracts routinely entered into by businesses in the ordinary course of business, contain provisions that impose liabilities or penalties of various types on the signatories. Such liabilities can be extremely onerous. Contracts of this kind may prove to have hidden penalties. Some companies sign the contracts under the impression that their current insurance policies will protect them in the event of the provision being invoked against them. However, most insurance contracts very carefully avoid extending the scope of cover beyond that specifically prescribed in the original policy contract. A company may therefore find itself facing an extremely heavy and unanticipated liability claim bereft of any insurance protection.</p>
	<p style="text-align: justify;">Examples of imposed liabilities in common use are those embodied in the standard agreements used for leases and by the construction and fire protection industries. These agreements render the purchase and/or owner and/or lessee responsible for the costs of injuries to people or damage to property caused by the activities of other parties or the malfunction of equipment. It is essential then, that when a business' legal adviser believes the contract a business is contemplating signing may have insurance implications, the contract be given to us for analysis on this score. Under certain circumstances, your insurance can be endorsed to provide cover. </p>
	<p style="text-align: justify;"><b>Claims Made - v - Occurrence policy wordings for Professional Indemnity Insurance and Management Liability Insurance policies </b></p>
	<p style="text-align: justify;">Professional Indemnity Insurance and Management Liability Insurance policies are different to normal Public/Products Liability Insurance policies. Under most normal Public/Products Liability Insurance policies, you can lodge a claim on a previous policy up to 7 years prior - even if you only become aware of the claim today. (Always of course, on the proviso that the property damage or personal injury occurred when the previous policy was in force). </p>
	<p style="text-align: justify;">This is NOT the case with Professional Indemnity Insurance and Management Liability Insurance policies however. Cover under these policies is on a "claims made" basis. This means you must always keep a current policy if you are ever to lodge a claim. You can't go back and lodge a claim under your lapsed policy for an event that occurred for example, last year, that you only became aware of this year, once your policy has expired. Even if the event occurred, last year, it would be claimed under this year's policy as this is the year the claim was first "made" upon you. Hence, it is important to keep Professional Indemnity Insurance and Management Liability Insurance policies renewed every year. </p>
	<p style="text-align: justify;"><b>If you are aware of any incident that could potentially become a claim, you must inform the insurer prior to the policy expiring - failure to do so on an incident you were aware of, means you will never be able to lodge a claim for it, even though you renewed the policy again. </b></p>
	<p style="text-align: justify;">If you have retired from work and are no longer taking on new work, you should renew the policies under a "run-off basis" for at least 3 years and perhaps longer. Whilst run-off policies still require a premium to be paid, this may be less expensive than your previous year's cost, & usually become cheaper for each subsequent year you choose to renew. The valuable benefit to you is that your policy will continue to provide necessary protection against new notifications of claims where the event occurred whilst you were still working.</p><br/><br/>
	<p style="text-align: center;"><b>Important details about your advisor and Action Insurance Brokers P/L</b></p>
	<p style="text-align: justify;"><b>About Your Advisor:</b><br/>This person is authorised to provide advice by Action Insurance Brokers P/L , to arrange General Insurance products for their clients. We do not arrange Life Insurance Products. Action Insurance Brokers P/L is licensed as a General Insurance Broker under Financial Services Reform Act 2001 (Cth). </p>
	<p style="text-align: justify;"><b>The type and limit of Advice we are providing you in relation to this policy:</b><br/>We are/have arranged/renewed this policy for you using information you have provided to us in the past or more recently. in doing so, we have relied on the integrity of that information. Further, we have used our technical knowledge and skills to ensure you have a competitively priced cover that meets your needs as we understand them. As the policy holder, it is your responsibility to review the cover and ensure it meets your needs. We ask now that you read this invoice, the policy wording and Product Disclosure Statement (where it has been provided as a legal requirement) and contact our office or your advisor immediately if you have any concerns about the adequacy of this policy. If you have any questions about your policy, its terms, conditions, restrictions or otherwise, please contact our office and your advisor will be pleased to answer your questions.</p>
	<p style="text-align: justify;"><b>Our Advice to You is limited:</b><br/>As your insurance broker, we are quoting on, or arranging, or renewing or changing this insurance policy, based on your instructions or based on information you have previously provided to us by way of face to face meetings, verbal or written communications. We endeavour at all times to ensure the policy we recommend or provide, meets your needs, to the extent that you have informed us of those needs. We cannot be held liable, or responsible for deficiencies in this insurance policy where you have failed to provide us with information that affects your insurance needs or this insurance policy. Whenever you have asked us to reduce covers, we have sought and obtained from you written advice to do this. We do not reduce covers without your written instruction.</p>
	<p style="text-align: justify;"><b>Our Liability to You is limited:</b><br/>The liability of Action Insurance Brokers P/L, it's Directors, Employees, Individual and Corporate Authorised Representatives and their Employees or Agents (herein after referred to as "Action"), to our clients, their Directors and Employees, Contractors and Sub Contractors and their Employees or Agents, is hereby limited to the extent permitted under Federal, State and Territory laws, to a maximum aggregate limit of $10,000,000 Australian Dollars, including legal and investigative costs. At no time shall "Action" agree to or be held liable for any amount over and above this limit. In appointing, dealing with, instructing "Action", or renewing, amending or endorsing a policy with "Action", or paying a premium to "Action", or paying a monthly instalment to a premium funding provider where this was arranged by "Action", as our client, you hereby agree to the above Limit of Liability and your agreement is irrevocable.</p>
	<p style="text-align: justify;"><b>General Advice Warning - Renewal of a policy:</b><br/>By arranging this policy for you, Action Entertainment Insurance Pty Ltd have not taken into account any specific business or personal information relating to your financial objectives, risk profile or specific needs for insurance. It is your responsibility as the policy holder / insured party, to ensure this policy is suitable for your needs. If you require specific advice as to your needs or the suitability of this policy, please feel free to call Action Entertainment Insurance.</p>
	<p style="text-align: justify;"><b>How we earn our income:</b><br/>As insurance brokers we may earn commission from the insurance company we are arranging this insurance policy with. We may also charge a broker fee to you to cover work we do on your behalf. We may also earn bonus commissions and bonus payments. Where we have arranged for you to pay this insurance policy premium by monthly instalments through a premium funding company, we may also earn commission for arranging this. We are also permitted to earn and retain bank account interest on the premium you pay into our regulated Insurance Broking Account. However, we are also responsible to pay the bank fees and charges on this account. The amount of commission and/or fees we have earned on this policy (not including any premium funding commission or bonus payments, are shown on the front page of this invoice.</p>
	<p style="text-align: justify;"><b>Influencing Associations:</b><br/>Action is part of the Steadfast Group. This is a publicly listed company whose sole purpose is to assist Insurance Brokers to arrange better cover and policies conditions for our clients and to ensure up to date training is provided to our advisors and other team members. As part of this arrangement, we can earn additional commission and rebates on policies we arrange with Steadfast approved insurance companies. Action also enjoys commercial arrangements with a number of insurance companies who may at times pay us a profit share or provide to our Advisors and team members, gifts, invitations to social and sporting functions and free training. We guarantee never to allow any other association with Insurance Company, Steadfast or other Group, to negatively influence our recommendations to you.</p>
	<p style="text-align: justify;"><b>Conflict of Interest Declaration:</b><br/>Action Insurance Brokers follows a strict set of guidelines that ensure you, our client are not subject to any conflict of interest where our Advisors may recommend the wrong policy for You. We will always recommend the product and insurer that best meets your needs based on the information you have supplied us; regardless of the rate or amount or nature of income that we may derive. In many cases, this will mean we will recommend a product from which we earn less income than other potential products. As from 31st March, 2014, a subsidiary company of Action Insurance Brokers - Action Entertainment Insurance P/L, owns 100% of Arena Underwriting P/L. Where we place your business with Arena on your behalf, certain persons within the Action Group may ultimately receive profit dividends as an indirect result of placing your business with Arena.</p>
	<p style="text-align: justify;"><b>Action's Own Financial Services Guide:</b><br/>Where the purchase of this product represents the first time you or your broker have used Action Insurance Brokers'services, we bring to your attention the need to read our Financial Services Guide. Our guide can be found by referring to our website - <a href="https://www.actioninsurance.com.au/">www.actioninsurance.com.au</a>. From there, you can select the Financial Services Guide page and use the drop-down box to select the Financial Services Guide provided by the Advisor or Authorised Representative of Action, who has provided this service to you.</p>
	<p style="text-align: justify;"><b>Cancelling, Changing or Amending this Insurance Policy:</b><br/>If you choose for any reason to cancel this policy before it has run its full term, you must give us a written request and the cancellation of the policy will only take effect from the date we receive that request. A written request is a letter, fax or email. Letters and faxes must be signed and all advices must include the policy number of the policy you want cancelled and a reason must be given, i.e, property sold or business ceased trading etc. Refunds will only be sent to you once the premium has been received from the Insurance Company and only if the policy does not include a "minimum and deposit" clause - wherein refunds of premium can never been obtained from the insurer. The refund we send will not include any return of commissions or fees we have earned. Such commissions and fees are retained by us in full. If you wish to change any thing covered under this policy, please give us advice in writing by letter, fax or email. The change will be effected from the date we receive your advice. It cannot be back-dated and it cannot be arranged outside business hours which are listed in our Financial Services Guide.</p>
	<p style="text-align: justify;"><b>Credit Card Payments:</b><br/>We welcome your payment of the premium by VISA or Master Card. We do not accept Diners Card or AMEX. Due to the increasing use of credit cards by our clients, we must now charge a 1.5% loading on top of your premium if you choose to pay be credit card, to cover the costs imposed upon us by the Bank. We regret this action has become necessary.</p>
	<p style="text-align: justify;"><b>Your Personal Information:</b><br/>Action Insurance Brokers and its Authorised representatives have and adhere to a privacy policy, which will ensure the privacy and security of your personal information. A copy of our privacy policy is available on request. A copy is also available on our website, <a href="https://www.actioninsurance.com.au/" target="_blank">www.entertainmentinsurance.net.au</a></p>
EOD;
$pdf->writeHTMLCell(0, 0, '', '', $sub_pages, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$filepath = 'components/pdf/'.$filename;
$path = JPATH_ROOT.'/'.$filepath;
$pdf->Output($path, 'F');
// $pdf->Output(JPATH_ROOT.'/components/pdf/test.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	if (file_exists($path)) {
		return $path;
	}

}

function gernateCertificatPDF($userdata){


		/**
		 * Creates an example PDF TEST document using TCPDF
		 * @package com.tecnick.tcpdf
		 * @abstract TCPDF - Example: Default Header and Footer
		 * @author Nicola Asuni
		 * @since 2008-03-04
		 */
		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->chargecurrency = $componentParams->get('chargecurrency');
		$this->insurername = $componentParams->get('insurername');
		$this->insureraddress = $componentParams->get('insureraddress');
		$this->abn = $componentParams->get('abn');
		
		//$data = unserialize($userdata->data);
		$newdataArr = unserialize($userdata->data);
		foreach ($newdataArr as $page => $pvalue) {
  			foreach ($pvalue as $key => $value) {
  					if(is_array($value)){
  						$nvalue = '';
  						foreach ($value as $ackey => $acvalue) {
  							$value[$ackey] = str_replace("&#039;", "'", $acvalue);
  							// $value[$ackey] = str_replace("&#039;", '"', $acvalue);
  							$nvalue = $value;
  						}
  						$newArr[$page][$key] = $nvalue;
  					}else{
  						$newArr[$page][$key] = str_replace("&#039;", "'", $value);
  					}
  				// $newArr[$page][$key] = addslashes($value);
  			}
  		}
  		
  		$data = $newArr;
  		// echo "<pre>";
		// print_r($userdata);
		// print_r($data);
		// echo "</pre>";
		$class_of_policy = "Perform-Sure Liability Insurance";
		$policy_no = $userdata->policy_number;
		$invoice_no = "290854";
		$invoice_date = date("d/m/Y");
		$reference = "ROBINSONKA";
		$insured = $data['page4']['insuredname'];
		$addressline1 = $data['page4']['streetaddress1'];
		$addressline2 = $data['page4']['streetaddress2'];
		$suburb = $data['page4']['suburb'];
		$this->statename = $this->getStateName();
		$state = '';
		foreach ($this->statename as $key => $value) {
			if($value->id == $data['page4']['state']){
				$state =  $value->name;
			}	
		}

		$postcode = $data['page4']['postcode'];
		$startdate = date("d/m/Y",strtotime($data['page1']['start_date']));
		$enddate = date("d/m/Y",strtotime('+1 year',strtotime($data['page1']['start_date'])));
		if(!empty($data['page2']['premium'])){
			$premium = '$'.$data['page2']['premium'];
		}else{
			$premium = '$0.00';
		}
		
		$totalgst = round($data['page2']['gstpremium']+$data['page2']['gstfee'],2); 
		if(!empty($totalgst)){
			$gst = '$'.$totalgst;
		}else{
			$gst = '$0.00';
		}

		if(!empty($data['page2']['stampduty'])){
			$stampduty = '$'.$data['page2']['stampduty'];
		}else{ 
			$stampduty = '$0.00'; 
		}

		if(!empty($data['page2']['brokerfee'])){
			$brokerfee = '$'.$data['page2']['brokerfee'];	
		}else{
			$brokerfee = '$0.00';	
		}
		/* get value from Backend */
		$currency = $this->chargecurrency;
		$insurername = $this->insurername;
		$insureraddress = $this->insureraddress;
		$abn = $this->abn;
		/* End Backend */

		$tp = number_format((float)$data['page6']['subtotal'], 2, '.', '');
		if(!empty($tp)){
			$totalpremium = '$'.$tp;
		}else{
			$totalpremium = '$0.00';
		}

		$totalcommission = round($data['page2']['commission']+$data['page2']['gstcommission'],2); 
		if(!empty($totalcommission)){
			$commission = '$'.$totalcommission;	
		}else{
			$commission = '$0.00';
		}
		
		$creditcardfee = '$'.round($data['page6']['subtotal']*1.5/100,2);
		$activity = '';
		foreach ($data['page1']['activity'] as $avalue) {
			$activity .= $avalue.',';
		}
		$activity = substr($activity,0,-1);
		$this->perfomers = $this->getTerm('Perfomers');
		foreach ($this->perfomers as $pvalue) {
			if($data['page1']['perfomers'] == $pvalue->price){
				$perf =  $pvalue->name;
			}
		}
		/* OLD */
			//$performers = $perf;
		/* NEW */
			$performers = $data['page1']['perfomers'];
		// $performers = $perf;
		if(!empty($data['page6']['publicliailitycover']) && (strpos($data['page6']['publicliailitycover'],'$') !== False)){
			$publicliability = $data['page6']['publicliailitycover'];
		}else if(!empty($data['page6']['publicliailitycover'])){
			$publicliability = number_format($data['page6']['publicliailitycover']);
		}else{
			$publicliability = '$0.00';
		}

		$this->Extracharegs = $this->getTerm('Excharge');
		$this->ExtraCharegsByOrderId = $this->getExtraFieldByOrder($userdata->id);
		$charges = 0;
		if(!empty($this->Extracharegs)){
			$Extrachreges = $this->Extracharegs;
			foreach ($Extrachreges as $evalue) {
				$charges += $evalue->price;
			}
		}

		if(!empty($this->ExtraCharegsByOrderId)){
			$ChregesByOrderId = $this->ExtraCharegsByOrderId;
			foreach ($ChregesByOrderId as $cvalue) {
				$charges += $cvalue->price;
			}
		}
		if(!empty($charges)){
			$TotalExtras = '$'.round($charges,2);	
		}else{
			$TotalExtras = '$0.00';	
		}
		$gernatedate = date('dmYHis');
		// $filename = $policy_no.'_'.$gernatedate.'.pdf';
		$filename = 'Certificate of Currency '.$policy_no.'.pdf';
		// require_once('tcpdf/tcpdf.php');
		require_once(JPATH_ROOT.'/components/pdf/tcpdf_extend.php');

		// create new PDF document
		$pdf2 = new TCPDF_EXTEND(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

		$pdf2->class_of_policy = "Perform-Sure Liability Insurance";
		$pdf2->insurername = $insurername;
		$pdf2->insureraddress = $insureraddress;
		$pdf2->abn = $abn;
		$pdf2->policy_no = $policy_no;
		$pdf2->invoice_date = $invoice_date;
		$pdf2->invoice_no = $invoice_no;
		$pdf2->reference = $reference;
		$pdf2->insured = $insured;
		$pdf2->addressline1 = $addressline1;
		$pdf2->addressline2 = $addressline2;
		$pdf2->suburb = $suburb;
		$pdf2->states = $state;
		$pdf2->postcode = $postcode;
		$pdf2->startdate = $startdate;
		$pdf2->enddate = $enddate;
		$pdf2->premium = $premium;
		$pdf2->gst = $gst;
		$pdf2->stampduty = $stampduty;
		$pdf2->brokerfee = $brokerfee;
		$pdf2->currency = $currency;
		$pdf2->totalpremium = $totalpremium;
		$pdf2->commission = $commission;
		$pdf2->creditcardfee = $creditcardfee;
		$pdf2->activity = $activity;
		$pdf2->performers = $performers;
		$pdf2->publicliability = $publicliability;
		$pdf2->totalextras = $TotalExtras;
		$pdf2->certificatissuedate = date('j F Y');
		

		// set document information
		$pdf2->SetCreator(PDF_CREATOR);
		$pdf2->SetAuthor('Insurance');
		$pdf2->SetTitle('Insurance Test PDF');
		$pdf2->SetSubject('Test Subject');
		$pdf2->SetKeywords('Insurance');

		$pdf2->setPrintFooter(true);

		// set default monospaced font
		// $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf2->SetMargins(5, 10, 5);
		$pdf2->SetHeaderMargin(10);
		$pdf2->SetFooterMargin(10);

		$pdf2->SetAutoPageBreak(TRUE, 5);

		$pdf2->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf2->setPageOrientation('P',TRUE,10);

		// set some language-dependent strings (optional)
		// if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		// 	require_once(dirname(__FILE__).'/lang/eng.php');
		// 	$pdf->setLanguageArray($l);
		// }

		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf2->setFontSubsetting(true);

		$pdf2->SetFont('times', '', 11, '', true);


$pdf2->AddPage();
$first_page = <<<EOD
	<table width="100%">
		<tr>
			<td width="40%" style="float:left;"><img src="components/pdf/new_logo.png" /></td>
			<td width="10%">&nbsp;</td>
			<td width="40%" style="float:right;"><img src="components/pdf/arena_logo.png" /></td>
		</tr>
	</table>
	<br/>
	<table width="100%" cellpadding="10">
		<tr style="text-align: center;">
			<td><b><u style="font-size: 15px;">CERTIFICATE OF CURRENCY</u></b></td>
		</tr>
	</table>
	<br/>
	<table width="100%" cellpadding="5">
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Reference number:</b> </td>
			<td width="50%">$pdf2->policy_no</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Insurance type:</b> </td>
			<td width="50%">Performsure Public & Products Liability Insurance</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Policy wording:</b> </td>
			<td width="50%">BIA GL G2 Arena Ent-2016</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Named Insured:</b> </td>
			<td width="50%">$pdf2->insured</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Insured Business:</b> </td>
			<td width="50%">$pdf2->activity</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Number of Performers:</b> </td>
			<td width="50%">$pdf2->performers</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Policy Period:</b> </td>
			<td width="50%">
				<span>From: $pdf2->startdate</span><br/>
				<span>To: $pdf2->enddate at 4pm</span><br/>
				<span>Local Standard Time where this policy is issued</span>
			</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Indemnity Limit:</b> </td>
			<td width="50%">
				<span>Section 1 - Public Liability</span><br/>
				<span>$ 20,000,000 Any One Occurrence</span><br/>
				<br/>
				<span>Section 2 - Products Liability</span><br/>
				<span>$ 20,000,000 Any One Period of Insurance (Aggregate)</span>
			</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Excess:</b> </td>
			<td width="50%">
				<table width="100%" cellpadding="">
					<tr><td>Section 1 - Public Liability</td></tr>
					<tr><td width="70%">Each & every occurrence: </td><td>$500</td></tr>
					<tr><td>Defence Costs inclusive</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td width="70%">Section 2 - Products Liability: </td><td>$500</td></tr>
					<tr><td>Defence Costs inclusive</td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Jurisdictional Limits:</b> </td>
			<td width="50%">World Wide excluding North America</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><b>Insurer:</b> </td>
			<td width="50%">
				<span>Arena Underwriting Pty Ltd (AFS 317617) on behalf of</span><br/>
				<span>Berkley Insurance Australia ABN $pdf2->abn</span><br/>
				<span>AFSL No. 463129</span>
			</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="35%"><img src="components/pdf/signature.png" /></td>
			<td width="50%">&nbsp;</td>
		</tr>
	</table>
	<br/>
	<table width="100%">
		<tr>
			<td width="5%"></td>
			<td width="90%">
				<hr style="height:2px;" />&nbsp;<br/>
				<table width="100%">
					<tr>
						<td>Signed as an Authorised Representative on behalf of Arena Underwriting Pty Ltd.</td>	
					</tr>
					<tr style="padding-top:50px;">
						<td>Date of issue: $pdf2->certificatissuedate</td>	
					</tr>
					<br/>
					<tr>
						<td>
							<span>Issued By Action Entertainment Insurance Pty Ltd Corporate Representative No. 237473 as an<br/>authorised representative of Action Insurance Brokers P/L ABN $pdf2->abn AFS 225047</span>
						</td>	
					</tr>
					<tr><td><b>Phone: 1300 655 424</b></td></tr>
					<tr><td><b>Fax: +61 2 8935 1501</b></td></tr>
					<tr><td><b>Email:</b> <a href="mailto:entertainment@actioninsurance.com.au">entertainment@actioninsurance.com.au</a></td></tr>
					<tr><td>Suite 301, Building A, 20 Lexington Drive, Bella Vista NSW 2153 Web: <a href="www.actioninsurance.com.au">www.actioninsurance.com.au</a></td></tr>
				</table>
			</td>
			<td width="5%"></td>
		</tr>
	</table>
EOD;
$pdf2->writeHTMLCell(0, 0, '', '', $first_page, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$filepath = 'components/pdf/'.$filename;
$path = JPATH_ROOT.'/'.$filepath;
$pdf2->Output($path, 'F');
// $pdf->Output(JPATH_ROOT.'/components/pdf/test.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	if (file_exists($path)) {
		return $path;
	}

}

function gernateQuestonnairePDF($userdata){


		/**
		 * Creates an example PDF TEST document using TCPDF
		 * @package com.tecnick.tcpdf
		 * @abstract TCPDF - Example: Default Header and Footer
		 * @author Nicola Asuni
		 * @since 2008-03-04
		 */
		$componentParams = &JComponentHelper::getParams('com_event_manage');
		$this->chargecurrency = $componentParams->get('chargecurrency');
		$this->insurername = $componentParams->get('insurername');
		$this->insureraddress = $componentParams->get('insureraddress');
		$this->abn = $componentParams->get('abn');
		
		//$data = unserialize($userdata->data);
		$newdataArr = unserialize($userdata->data);
		foreach ($newdataArr as $page => $pvalue) {
  			foreach ($pvalue as $key => $value) {
  					if(is_array($value)){
  						$nvalue = '';
  						foreach ($value as $ackey => $acvalue) {
  							$value[$ackey] = str_replace("&#039;", "'", $acvalue);
  							// $value[$ackey] = str_replace("&#039;", '"', $acvalue);
  							$nvalue = $value;
  						}
  						$newArr[$page][$key] = $nvalue;
  					}else{
  						$newArr[$page][$key] = str_replace("&#039;", "'", $value);
  					}
  				// $newArr[$page][$key] = addslashes($value);
  			}
  		}
  		
  		$data = $newArr;
  		// echo "<pre>";
		//print_r($userdata);
		// print_r($data);
		// echo "</pre>";
		// die;
		$class_of_policy = "Perform-Sure Liability Insurance";
		$policy_no = $userdata->policy_number;
		$invoice_no = "290854";
		$invoice_date = date("d/m/Y");
		$reference = "ROBINSONKA";
		$FirstName = $data['page4']['firstname'];
		$LastName = $data['page4']['lastname'];
		$insured = $data['page4']['insuredname'];
		$addressline1 = $data['page4']['streetaddress1'];
		$addressline2 = $data['page4']['streetaddress2'];
		$suburb = $data['page4']['suburb'];
		/*Page3 Data */
		if($data['page3']['selfpromote'] == 'Y'){
			$selfpromote = 'YES';
		}else if($data['page3']['selfpromote'] == 'N'){
			$selfpromote = 'NO';
		}

		if($data['page3']['harmlessorindemnity'] == 'Y'){
			$harmlessorindemnity = 'YES';
		}else if($data['page3']['harmlessorindemnity'] == 'N'){
			$harmlessorindemnity = 'NO'; 
		}

		if($data['page3']['subcontractors'] == 'Y'){
			$subcontractors = 'YES';
		}else if($data['page3']['subcontractors'] == 'N'){
			$subcontractors = 'NO';
		}

		if($data['page3']['refusedinsurance'] == 'Y'){
			$refusedinsurance = 'YES';
		}else if($data['page3']['refusedinsurance'] == 'N'){
			$refusedinsurance = 'NO';
		}

		if($data['page3']['liabilityclaim'] == 'Y'){
			$liabilityclaim = 'YES';
		}else if($data['page3']['liabilityclaim'] == 'N'){
			$liabilityclaim = 'NO';
		}

		if($data['page3']['criminaloffence'] == 'Y'){
			$criminaloffence = 'YES';
		}else if($data['page3']['criminaloffence'] == 'N'){
			$criminaloffence = 'NO';
		}

		if($data['page3']['dangerousactivities'] == 'Y'){
			$dangerousactivities = 'YES';
		}else if($data['page3']['dangerousactivities'] == 'N'){
			$dangerousactivities = 'NO';
		}

		if($data['page3']['pyrotechnics'] == 'Y'){
			$pyrotechnics = 'YES';
		}else if($data['page3']['pyrotechnics'] == 'N'){
			$pyrotechnics = 'NO';
		}

		if($data['page3']['animals'] == 'Y'){
			$animals = 'YES';
		}else if($data['page3']['animals'] == 'N'){
			$animals = 'NO';
		}

		if($data['page3']['amusementrides'] == 'Y'){
			$amusementrides = 'YES';
		}else if($data['page3']['amusementrides'] == 'N'){
			$amusementrides = 'NO';
		}

		if($data['page3']['workshops'] == 'Y'){
			$workshops = 'YES';
		}else if($data['page3']['workshops'] == 'N'){
			$workshops = 'NO';
		}

		if($data['page3']['northamerica'] == 'Y'){
			$northamerica = 'YES';
		}else if($data['page3']['northamerica'] == 'N'){
			$northamerica = 'NO';
		}
		/*Page3 End Data */

		$this->statename = $this->getStateName();
		$state = '';
		foreach ($this->statename as $key => $value) {
			if($value->id == $data['page4']['state']){
				$state =  $value->name;
			}	
		}

		$postcode = $data['page4']['postcode'];
		$startdate = date("d/m/Y",strtotime($data['page1']['start_date']));
		$enddate = date("d/m/Y",strtotime('+1 year',strtotime($data['page1']['start_date'])));
		if(!empty($data['page2']['premium'])){
			$premium = '$'.$data['page2']['premium'];
		}else{
			$premium = '$0.00';
		}
		
		$totalgst = round($data['page2']['gstpremium']+$data['page2']['gstfee'],2); 
		if(!empty($totalgst)){
			$gst = '$'.$totalgst;
		}else{
			$gst = '$0.00';
		}

		if(!empty($data['page2']['stampduty'])){
			$stampduty = '$'.$data['page2']['stampduty'];
		}else{ 
			$stampduty = '$0.00'; 
		}

		if(!empty($data['page2']['brokerfee'])){
			$brokerfee = '$'.$data['page2']['brokerfee'];	
		}else{
			$brokerfee = '$0.00';	
		}
		/* get value from Backend */
		$currency = $this->chargecurrency;
		$insurername = $this->insurername;
		$insureraddress = $this->insureraddress;
		$abn = $this->abn;
		/* End Backend */

		$tp = number_format((float)$data['page6']['subtotal'], 2, '.', '');
		if(!empty($tp)){
			$totalpremium = '$'.$tp;
		}else{
			$totalpremium = '$0.00';
		}

		$totalcommission = round($data['page2']['commission']+$data['page2']['gstcommission'],2); 
		if(!empty($totalcommission)){
			$commission = '$'.$totalcommission;	
		}else{
			$commission = '$0.00';
		}
		
		$creditcardfee = '$'.round($data['page6']['subtotal']*1.5/100,2);
		$activity = '';
		foreach ($data['page1']['activity'] as $avalue) {
			$activity .= $avalue.',';
		}
		$activity = substr($activity,0,-1);
		$this->perfomers = $this->getTerm('Perfomers');
		foreach ($this->perfomers as $pvalue) {
			if($data['page1']['perfomers'] == $pvalue->price){
				$perf =  $pvalue->name;
			}
		}
		$performers = $perf;
		if(!empty($data['page6']['publicliailitycover'])){
			$publicliability = number_format($data['page6']['publicliailitycover']);
		}else{
			$publicliability = '$0.00';
		}

		$this->Extracharegs = $this->getTerm('Excharge');
		$this->ExtraCharegsByOrderId = $this->getExtraFieldByOrder($userdata->id);
		$charges = 0;
		if(!empty($this->Extracharegs)){
			$Extrachreges = $this->Extracharegs;
			foreach ($Extrachreges as $evalue) {
				$charges += $evalue->price;
			}
		}

		if(!empty($this->ExtraCharegsByOrderId)){
			$ChregesByOrderId = $this->ExtraCharegsByOrderId;
			foreach ($ChregesByOrderId as $cvalue) {
				$charges += $cvalue->price;
			}
		}
		if(!empty($charges)){
			$TotalExtras = '$'.round($charges,2);	
		}else{
			$TotalExtras = '$0.00';	
		}
		$gernatedate = date('dmYHis');
		// $filename = $policy_no.'_'.$gernatedate.'.pdf';
		$filename3 = 'Performsure - Questionnaire Response Summary - '.$policy_no.'.pdf';
		// require_once('tcpdf/tcpdf.php');
		require_once(JPATH_ROOT.'/components/pdf/tcpdf_extend_questionnaire.php');

		// create new PDF document
		$pdf3 = new TCPDF_EXTEND_QUESTIONNARIE(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

		$pdf3->class_of_policy = "Perform-Sure Liability Insurance";
		$pdf3->insurername = $insurername;
		$pdf3->insureraddress = $insureraddress;
		$pdf3->abn = $abn;
		$pdf3->policy_no = $policy_no;
		$pdf3->invoice_date = $invoice_date;
		$pdf3->invoice_no = $invoice_no;
		$pdf3->reference = $reference;
		$pdf3->insured = $insured;
		$pdf3->addressline1 = $addressline1;
		$pdf3->addressline2 = $addressline2;
		$pdf3->suburb = $suburb;
		$pdf3->states = $state;
		$pdf3->postcode = $postcode;
		$pdf3->startdate = $startdate;
		$pdf3->enddate = $enddate;
		$pdf3->premium = $premium;
		$pdf3->gst = $gst;
		$pdf3->stampduty = $stampduty;
		$pdf3->brokerfee = $brokerfee;
		$pdf3->currency = $currency;
		$pdf3->totalpremium = $totalpremium;
		$pdf3->commission = $commission;
		$pdf3->creditcardfee = $creditcardfee;
		$pdf3->activity = $activity;
		$pdf3->performers = $performers;
		$pdf3->publicliability = $publicliability;
		$pdf3->totalextras = $TotalExtras;
		$pdf3->questionnairedate = date('j F Y');
		/*Page3 Data*/
		$pdf3->selfpromote = $selfpromote;
		$pdf3->harmlessorindemnity = $harmlessorindemnity;
		$pdf3->subcontractors = $subcontractors;
		$pdf3->refusedinsurance = $refusedinsurance;
		$pdf3->liabilityclaim = $liabilityclaim;
		$pdf3->criminaloffence = $criminaloffence;
		$pdf3->dangerousactivities = $dangerousactivities;
		$pdf3->pyrotechnics = $pyrotechnics;
		$pdf3->animals = $animals;
		$pdf3->amusementrides = $amusementrides;
		$pdf3->workshops = $workshops;
		$pdf3->northamerica = $northamerica;
		/*End Page3 Data*/
		
		$pdf3->FirstName = ucfirst($FirstName);
		$pdf3->LastName = ucfirst($LastName);

		// set document information
		$pdf3->SetCreator(PDF_CREATOR);
		$pdf3->SetAuthor('Insurance');
		$pdf3->SetTitle('Insurance Test PDF');
		$pdf3->SetSubject('Test Subject');
		$pdf3->SetKeywords('Insurance');

		$pdf3->setPrintFooter(true);

		// set default monospaced font
		// $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf3->SetMargins(5, 10, 5);
		$pdf3->SetHeaderMargin(10);
		$pdf3->SetFooterMargin(31);

		$pdf3->SetAutoPageBreak(TRUE, 5);

		$pdf3->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf3->setPageOrientation('P',TRUE,10);

		// set some language-dependent strings (optional)
		// if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		// 	require_once(dirname(__FILE__).'/lang/eng.php');
		// 	$pdf->setLanguageArray($l);
		// }

		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf3->setFontSubsetting(true);

		$pdf3->SetFont('times', '', 11, '', true);


$pdf3->AddPage();
$first_page = <<<EOD
	<table width="100%">
		<tr>
			<td align="center"><img src="components/pdf/new_logo.png" /></td>
		</tr>
	</table>
	<br/>
	<table width="100%" cellpadding="10">
		<tr>
			<td>Date: $pdf3->questionnairedate</td>
		</tr>
		<tr>
			<td>Policy Number: $pdf3->policy_no</td>
		</tr>
		<tr>
			<td>Dear $pdf3->FirstName&nbsp;$pdf3->LastName</td>
		</tr>
		<tr>
			<td>Thank you for purchasing your insurance with Action Entertainment Insurance.</td>
		</tr>
		<tr>
			<td>Please find below a summary of the responses you provided us when completing your Performsure Online Insurance Quotation</td>
		</tr>
	</table>
	<br/>
	<table width="100%" cellpadding="5">
		<tr><td><div><b>&nbsp;&nbsp;QUESTIONNAIRE RESPONSES</b></div></td></tr>
		<tr><td width="05%">&nbsp;</td><td width="95%">1. Do you intend to hire out a performance venue to self-promote or stage your own performance? <b>$pdf3->selfpromote</b> </td></tr><br/>
		<tr><td width="05%">&nbsp;</td><td width="95%">2. Will you be signing any contracts that contain hold harmless or indemnity agreements? <b>$pdf3->harmlessorindemnity</b> </td></tr><br/>
		<tr><td width="05%">&nbsp;</td><td width="95%">3. Do you use subcontractors? <b>$pdf3->subcontractors</b> </td></tr><br/>
		<tr><td width="05%">&nbsp;</td><td width="95%">4. Have you previously been refused insurance or had your insurance cancelled by an insurer or have had special <br/>&nbsp;&nbsp;&nbsp;&nbsp;conditions, increased premiums or increased excesses imposed on any policy of insurance by an insurer? <b>$pdf3->refusedinsurance</b> </td></tr><br/>
		<tr><td width="05%">&nbsp;</td><td width="95%">5. Have you suffered any public liability claims or have caused incidents that give rise to public liability claim? <b>$pdf3->liabilityclaim</b> </td></tr><br/>
		<tr><td width="05%">&nbsp;</td><td width="95%">6. Have you been charged or convicted of a criminal offence (excluding driving convictions) in the last 10 years? <b>$pdf3->criminaloffence</b> </td></tr><br/>
		<tr>
			<td width="05%">&nbsp;</td>
			<td>
				<div>7. Will your performance activities include:</div>
				<table width="100%" cellpadding="5">
					<tr>
						<td width="05%">&nbsp;</td>
						<td width="95%">
							<div>a. Audience participation with use of fire, sporting, hazardous or dangerous activities: <b>$pdf3->dangerousactivities</b> </div>
						</td>
					</tr>
					<tr>
						<td width="05%">&nbsp;</td>
						<td width="95%">
							<div>b. Fireworks or pyrotechnics: <b>$pdf3->pyrotechnics</b> </div>
						</td>
					</tr>
					<tr>
						<td width="05%">&nbsp;</td>
						<td width="95%">
							<div>c. Use of animals: <b>$pdf3->animals</b> </div>
						</td>
					</tr>
					<tr>
						<td width="05%">&nbsp;</td>
						<td width="95%">
							<div>d. Use of amusement rides or devices: <b>$pdf3->amusementrides</b> </div>
						</td>
					</tr>
					<tr>
						<td width="05%">&nbsp;</td>
						<td width="95%">
							<div>e. Running of workshops: <b>$pdf3->workshops</b> </div>
						</td>
					</tr>
					<tr>
						<td width="05%">&nbsp;</td>
						<td width="95%">
							<div>f. Any activities conducted in North America: <b>$pdf3->northamerica</b> </div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
EOD;
$pdf3->writeHTMLCell(0, 0, '', '', $first_page, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$filepath = 'components/pdf/'.$filename3;
$path = JPATH_ROOT.'/'.$filepath;
$pdf3->Output($path, 'F');
// $pdf->Output(JPATH_ROOT.'/components/pdf/questionnaire.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	if (file_exists($path)) {
		return $path;
	}

}


	// this function call for update user status
	function savepdffilename($oid,$filename){

		$db = JFactory::getDBO();
		$query = "UPDATE `#__event_manage_session` SET `policy_filename`= '".$filename."' $query WHERE `id` = '".$oid."'";
		$db->setQuery($query);
		$result = $db->execute();
		return $result;	
	
	}

}