<?php
/**
 * @version		3.0.0
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

require_once JPATH_ADMINISTRATOR.'/components/com_k2/controller.php';

/**
 * Settings JSON controller.
 */

class K2ControllerSettings extends K2Controller
{
	/**
	 * Default implementation for save function.
	 * This function saves a row and then performs inside routing to fetch the data for the next screen.
	 * Create and update requests are routed here by the main Sync function.
	 * Usually there will be no need to override this function.
	 *
	 * @return void
	 */
	protected function save()
	{
		// Check for token
		JSession::checkToken() or K2Response::throwError(JText::_('JINVALID_TOKEN'));

		// Data
		$input = JRequest::get('post');

		// Get extension
		$component = JComponentHelper::getComponent('com_k2');

		// Prepare data for model
		$id = $component->id;
		$option = 'com_k2';
		$data = $input['jform'];

		// Use Joomla! model for saving settings
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_config/models');
		$model = JModelLegacy::getInstance('Component', 'ConfigModel');

		// Get form
		JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/com_k2');
		$form = JForm::getInstance('com_fpss.settings', 'config', array('control' => 'jform'), false, '/config');

		// Validate the posted data
		$return = $model->validate($form, $data);

		// Check for validation errors
		if ($return === false)
		{
			// Get the validation errors
			$errors = $model->getErrors();
			$message = $errors[0] instanceof Exception ? $errors[0]->getMessage() : $errors[0];
			K2Response::throwError($message);
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id' => $id,
			'option' => $option
		);
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save failed, go back to the screen and display a notice.
			K2Response::throwError(JText::sprintf('JERROR_SAVE_FAILED', $model->getError()));
		}
	}

}