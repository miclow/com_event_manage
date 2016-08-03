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
JFormHelper::loadFieldClass('list');

/**
 *Form_manage model.
 *
 * @since  1.6
 */
class Event_manageModelForms extends JModelAdmin
{
	

	public function getForm($data = array(), $loadData = true){
	
	    JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		
	    $form = $this->loadForm('com_event_manage.event', $data[0], array('control' => 'jform', 'load_data' => ''));
	    if (empty($form)) {
	        return false;
	    }
	    return $form;
	    
	}

	
}
