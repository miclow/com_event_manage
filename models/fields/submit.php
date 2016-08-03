<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Event_manage
 * @author     jainik <jainik@raindropsinfotech.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

/**
 * Class JFormFieldSubmit
 *
 * @since  1.6
 */
class JFormFieldSubmit extends JFormField
{
	protected $type = 'submit';

	protected $value;

	protected $for;

	/**
	 * Get a form field markup for the input
	 *
	 * @return string
	 */
	public function getInput()
	{
		$this->value = $this->getAttribute('value'); 

		return '<input type="submit" value="'.$this->value.'" id="submit" class="btn btn-primary" />';
		
	}
}
