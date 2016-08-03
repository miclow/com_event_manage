<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Event_manage
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::register('Event_manageFrontendHelper', JPATH_COMPONENT . '/helpers/event_manage.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Event_manage');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
