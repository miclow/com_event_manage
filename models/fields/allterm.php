<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Event_manage
 * @author     jainik <jainik@raindropsinfotech.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 *
 * @since  1.6
 */
class JFormFieldAllterm extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'allterm';

	/**
	 * Method to get the field input markup.
	 *
	 * @return   string  The field input markup.
	 *
	 * @since    1.6
	 */
	protected function getInput()
	{
		$event =  JModelLegacy::getInstance('event','Event_manageModel');	
		$terms = $event->getTerm();			
		// Initialize variables.
		$html = array();

		if (count($terms)>0)
		{
			$html[] = '<select id="term" name="'.$this->name.'">';
			foreach ($terms as $key => $value) {
				$html[] = '<option value="'.$value->id.'">'.$value->name.'</option>';
			}
			$html[] = '</select>';
			
		}
		else
		{
			$html[] = '<select id="term" name="term" >';
		
			$html[] = '</select>';
		}		
		return implode($html);
	}
      
        
}
