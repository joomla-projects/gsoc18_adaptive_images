<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\Media\Administrator\AdaptiveImage\FocusStoreInterface;
use Joomla\Component\Media\Administrator\AdaptiveImage\JSONFocusStore;

/**
 * Adaptive Image Controller Class
 *
 * Used to set the focus point and save it into filesystem
 *
 * Used to get the focus point while rendering the page on the frontend
 *
 * @since  4.0.0
 */
class AdaptiveImageController extends BaseController
{
	/**
	 * Execute a task by triggering a method in the derived class.
	 *
	 * @param   string  $task  The task to perform.
	 *
	 * @return  boolean
	 *
	 * @since   4.0.0
	 */
	public function execute($task)
	{
		if ($task == "setfocus")
		{
			$imgPath = $this->input->getString('path');
			$dataFocus = array (
				"data-focus-top" 	=> $this->input->getFloat('data-focus-top'),
				"data-focus-left"	=> $this->input->getFloat('data-focus-left'),
				"data-focus-bottom" => $this->input->getFloat('data-focus-bottom'),
				"data-focus-right"	=> $this->input->getFloat('data-focus-right')
			);
			$storage = new JSONFocusStore;
			return $this->performTask($storage, $dataFocus, $imgPath);
		}

	}
	/**
	 * Call to the method of respective	class for the respective object passed
	 * 
	 * @param   FocusStoreInterface  $storage    Storage Object
	 * @param   Array                $dataFocus  All the data focus points for image
	 * @param   String               $imgPath    Image Path
	 * 
	 * @return boolean
	 * 
	 * @since 4.0.0
	 */
	protected function performTask(FocusStoreInterface $storage, $dataFocus, $imgPath)
	{
		return $storage->setFocus($dataFocus, $imgPath);
	}
}
