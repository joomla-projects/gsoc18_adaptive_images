<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_workflow
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       __DEPLOY_VERSION__
 */

namespace Joomla\Component\Workflow\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\String\StringHelper;
use Joomla\CMS\Language\Text;

/**
 * Model class for workflow
 *
 * @since  __DEPLOY_VERSION__
 */
class WorkflowModel extends AdminModel
{
	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function populateState()
	{
		parent::populateState();

		$app       = Factory::getApplication();
		$context   = $this->option . '.' . $this->name;
		$extension = $app->getUserStateFromRequest($context . '.filter.extension', 'extension', 'com_content', 'cmd');

		$this->setState('filter.extension', $extension);
	}

	/**
	 * Method to change the title
	 *
	 * @param   integer  $category_id  The id of the category.
	 * @param   string   $alias        The alias.
	 * @param   string   $title        The title.
	 *
	 * @return	array  Contains the modified title and alias.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function generateNewTitle($category_id, $alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();

		while ($table->load(array('title' => $title)))
		{
			$title = StringHelper::increment($title);
		}

		return array($title, $alias);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean True on success.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function save($data)
	{
		$user					= Factory::getUser();
		$app					= Factory::getApplication();
		$input                  = $app->input;
		$context				= $this->option . '.' . $this->name;
		$extension				= $app->getUserStateFromRequest($context . '.filter.extension', 'extension', 'com_content', 'cmd');
		$data['extension']		= $extension;
		$data['asset_id']		= 0;

		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();

			// Alter the title for save as copy
			if ($origTable->load(['title' => $data['title']]))
			{
				list($title) = $this->generateNewTitle(0, '', $data['title']);
				$data['title'] = $title;
			}

			// Unpublish new copy
			$data['published'] = 0;
		}

		$result = parent::save($data);

		// Create a default stage
		if ($result && $input->getCmd('task') !== 'save2copy' && $this->getState($this->getName() . '.new'))
		{
			$stage = $this->getTable('Stage');

			$newstage = new \stdClass;

			$newstage->workflow_id = (int) $this->getState($this->getName() . '.id');
			$newstage->title = Text::_('COM_WORKFLOW_PUBLISHED');
			$newstage->description = '';
			$newstage->published = 1;
			$newstage->condition = 1;
			$newstage->default = 1;

			$stage->save($newstage);
		}

		return $result;
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return \JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_workflow.workflow',
			'workflow',
			array(
				'control'   => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		if ($loadData)
		{
			$data = $this->loadFormData();
		}

		$item = $this->getItem($form->getValue('id'));

		// Deactivate switcher if default
		// Use $item, otherwise we'll be locked when we get the data from the request
		if (!empty($item->default))
		{
			$form->setFieldAttribute('default', 'readonly', 'true');
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		$form->setFieldAttribute('created', 'default', Factory::getDate()->format('Y-m-d H:i:s'));
		$form->setFieldAttribute('modified', 'default', Factory::getDate()->format('Y-m-d H:i:s'));

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed  The data for the form.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState(
			'com_workflow.edit.workflow.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to preprocess the form.
	 *
	 * @param   \JForm  $form   A \JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		$extension = Factory::getApplication()->input->get('extension', 'com_content');

		// Set the access control rules field component value.
		$form->setFieldAttribute('rules', 'component', $extension);
		$form->setFieldAttribute('rules', 'section', 'workflow');

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getReorderConditions($table)
	{
		return 'extension = ' . $this->getDbo()->q($table->extension);
	}

	/**
	 * Method to change the default state of one item.
	 *
	 * @param   array    $pk     A list of the primary keys to change.
	 * @param   integer  $value  The value of the home state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function setDefault($pk, $value = 1)
	{
		$table = $this->getTable();

		if ($table->load(array('id' => $pk)))
		{
			if ($table->published !== 1)
			{
				$this->setError(Text::_('COM_WORKFLOW_ITEM_MUST_PUBLISHED'));

				return false;
			}
		}

		$date = Factory::getDate()->toSql();

		if ($value)
		{
			// Unset other default item
			if ($table->load(array('default' => '1')))
			{
				$table->default = 0;
				$table->modified = $date;
				$table->store();
			}
		}

		if ($table->load(array('id' => $pk)))
		{
			$table->modified = $date;
			$table->default  = $value;
			$table->store();
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function canDelete($record)
	{
		if (empty($record->id) || $record->published != -2)
		{
			return false;
		}

		return Factory::getUser()->authorise('core.delete', $record->extension . '.workflow.' . (int) $record->id);
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function canEditState($record)
	{
		$user = Factory::getUser();

		// Check for existing workflow.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', $record->extension . '.workflow.' . (int) $record->id);
		}

		// Default to component settings if workflow isn't known.
		return $user->authorise('core.edit.state', $record->extension);
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function publish(&$pks, $value = 1)
	{
		$table = $this->getTable();
		$pks   = (array) $pks;

		$date = Factory::getDate()->toSql();

		// Default workflow item existence checks.
		foreach ($pks as $i => $pk)
		{
			if ($value != 1 && $table->default)
			{
				$this->setError(Text::_('COM_WORKFLOW_ITEM_MUST_PUBLISHED'));
				unset($pks[$i]);
				break;
			}

			$table->load($pk);
			$table->modified = $date;
			$table->store();
		}

		return parent::publish($pks, $value);
	}
}
