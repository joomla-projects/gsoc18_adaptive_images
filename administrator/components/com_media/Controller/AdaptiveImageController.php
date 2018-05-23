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

/**
 * Adaptive Image Controller
 * 
 * Used to get the focus point and save it into filesystem
 * 
 * Used to set the focus point while rendering the page on the frontend
 *
 * @since  4.0.0
 */
class AdaptiveImageController extends BaseController
{

	/** 
	 * 
	 * Function to get the focus point
	 * 
	 * @param array $dataFocus Array of the values of diffrent focus point
	 * 
	 * @param string $filePath Full path for the file
	 * 
	 * @return boolean
	 * 
	*/

	public function getFocus($dataFocus,$filePath)
	{

	}

	/**
	 * Function to set the focus point
	*/

	public function setFocus()
	{
		
	}
	
}
