<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Event_manage
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class Event_manageFrontendHelper
 *
 * @since  1.6
 */
class Event_manageFrontendHelper
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_event_manage/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_event_manage/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'Event_manageModel');
		}

		return $model;
	}
}
