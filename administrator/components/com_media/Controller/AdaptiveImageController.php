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
use Joomla\Component\Media\Administrator\FocusStore\FocusStoreClass;

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
	 * @param   string  $task     The task to perform.
	 * @param   string  $imgPath  Path of the image.
	 *
	 * @return  mixed   The value returned by the called method.
	 *
	 * @since   4.0.0
	 */
	public function execute($task, $imgPath = null)
	{
		if ($task == "setfocus")
		{
			$imgPath = $this->imageSrc();
			$dataFocus = array (
				"data-focus-top" => $_GET['data-focus-top'],
				"data-focus-left" => $_GET['data-focus-left'],
				"data-focus-bottom" => $_GET['data-focus-bottom'],
				"data-focus-right" => $_GET['data-focus-right']
			);
			$obj = new FocusStoreClass;
			$obj->setFocus($dataFocus, $imgPath);
		}
		elseif ($task == "getfocus" && $imgPath != null)
		{
			$obj = new FocusStoreClass;
			return $obj->getFocus($imgPath);
		}

	}
	/**
	 * Get the Image Path.
	 *
	 * index.php?option=com_media&task=adaptiveimage.getfocus&path=/images/sampledata/fruitshop/bananas_1.jpg
	 *
	 * @return  string
	 *
	 * @since   4.0.0
	 */
	public function imageSrc()
	{
		$src = $this->input->getString('path');

		return $src;
	}

}
