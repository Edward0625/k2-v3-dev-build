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

require_once JPATH_ADMINISTRATOR.'/components/com_k2/views/view.php';

/**
 * Categories JSON view.
 */

class K2ViewCategories extends K2View
{

	/**
	 * Builds the response variables needed for rendering a list.
	 * Usually there will be no need to override this function.
	 *
	 * @return void
	 */

	public function show()
	{
		// Set title
		$this->setTitle('K2_CATEGORIES');

		// Set user states
		$this->setUserStates();

		// Set rows
		$this->setRows();

		// Set pagination
		$this->setPagination();

		// Set filters
		$this->setFilters();

		// Set toolbar
		$this->setToolbar();

		// Set menu
		$this->setMenu();

		// Set Actions
		$this->setListActions();

		// Render
		parent::render();
	}

	/**
	 * Builds the response variables needed for rendering a form.
	 * Usually there will be no need to override this function.
	 *
	 * @param integer $id	The id of the resource to load.
	 *
	 * @return void
	 */

	public function edit($id = null)
	{
		// Set title
		$this->setTitle('K2_CATEGORY');

		// Set row
		$this->setRow($id);

		// Set form
		$this->setForm();

		// Set menu
		$this->setMenu('edit');

		// Set Actions
		$this->setFormActions();

		// Render
		parent::render();
	}

	protected function setUserStates()
	{
		$this->setUserState('limit', 10, 'int');
		$this->setUserState('page', 1, 'int');
		$this->setUserState('search', '', 'string');
		$this->setUserState('access', 0, 'int');
		$this->setUserState('state', '', 'cmd');
		$this->setUserState('language', '', 'string');
		$this->setUserState('sorting', 'ordering', 'string');
	}

	protected function setFilters()
	{

		// Language filter
		K2Response::addFilter('language', JText::_('K2_SELECT_LANGUAGE'), K2HelperHTML::language('language', '', 'K2_ANY'), false, 'header');

		// Sorting filter
		/*$sortingOptions = array(
		 'K2_ID' => 'id',
		 'K2_TITLE' => 'title',
		 'K2_ORDERING' => 'ordering',
		 'K2_STATE' => 'state',
		 'K2_AUTHOR' => 'author',
		 'K2_MODERATOR' => 'moderator',
		 'K2_ACCESS_LEVEL' => 'access',
		 'K2_CREATED' => 'created',
		 'K2_MODIFIED' => 'modified',
		 'K2_IMAGE' => 'image',
		 'K2_LANGUAGE' => 'language'
		 );
		 K2Response::addFilter('sorting', JText::_('K2_SORT_BY'), K2HelperHTML::sorting($sortingOptions), false, 'header');*/

		// Search filter
		K2Response::addFilter('search', JText::_('K2_SEARCH'), K2HelperHTML::search(), false, 'sidebar');

		// State filter
		K2Response::addFilter('state', JText::_('K2_STATE'), K2HelperHTML::state('state', null, true, false, 'K2_ALL'), true, 'sidebar');

	}

	protected function setToolbar()
	{
		// Check permissions for the current rows to determine if we should show the state togglers and the delete button
		$rows = K2Response::getRows();
		$canEditState = false;
		$canDelete = false;
		foreach ($rows as $row)
		{
			if ($row->canEditState)
			{
				$canEditState = true;
			}
			if ($row->canDelete)
			{
				$canDelete = true;
			}
		}
		if ($canEditState)
		{
			K2Response::addToolbarAction('publish', 'K2_PUBLISH', array(
				'data-value' => '1',
				'class' => 'appActionSetState',
				'id' => 'appActionPublish'
			));
			K2Response::addToolbarAction('unpublish', 'K2_UNPUBLISH', array(
				'data-value' => '0',
				'class' => 'appActionSetState',
				'id' => 'appActionUnpublish'
			));
			K2Response::addToolbarAction('trash', 'K2_TRASH', array(
				'data-value' => '-1',
				'class' => 'appActionSetState',
				'id' => 'appActionTrash'
			));
		}
		K2Response::addToolbarAction('batch', 'K2_BATCH', array('id' => 'appActionBatch'));
		if ($canDelete)
		{
			K2Response::addToolbarAction('remove', 'K2_DELETE', array('id' => 'appActionRemove'));
		}
	}

	protected function setFormFields(&$form, $row)
	{
		$form->state = K2HelperHTML::state('state', $row->state, true);
		$form->language = K2HelperHTML::language('language', $row->language);
		$form->access = JHtml::_('access.level', 'access', $row->access, '', false);
		$form->parent = K2HelperHTML::categories('parent_id', $row->parent_id, 'K2_NONE', $row->id);
		$form->inheritance = K2HelperHTML::categories('inheritance', $row->inheritance, 'K2_NONE', $row->id);
		$form->template = K2HelperHTML::template('template', $row->template);
		require_once JPATH_ADMINISTRATOR.'/components/com_k2/classes/editor.php';
		$config = JFactory::getConfig();
		$editor = K2Editor::getInstance($config->get('editor'));
		$form->description = $editor->display('description', $row->description, '100%', '300', '40', '5');
		require_once JPATH_ADMINISTRATOR.'/components/com_k2/helpers/extrafields.php';
		$form->extraFields = K2HelperExtraFields::getCategoryExtraFields($row->catid, $row->extra_fields);
	}

	/**
	 * Hook for children views to allow them set the menu for the list requests.
	 * Children views usually will not need to override this method.
	 *
	 * @return void
	 */
	protected function setListActions()
	{
		$user = JFactory::getUser();
		if ($user->authorise('k2.category.create', 'com_k2'))
		{
			K2Response::addAction('add', 'K2_ADD', array(
				'class' => 'appAction',
				'id' => 'appActionAdd'
			));
		}
	}

}
