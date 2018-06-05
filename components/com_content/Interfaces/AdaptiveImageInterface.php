<?php
/**
 * @package     Joomla
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Site\Interfaces;

defined('_JEXEC') or die;

/**
 * Adaptive Image Interface.
 *
 * @since  4.0.0
 */
interface AdaptiveImageInterface
{
	/**
	 * Pubic function for storeing the focus points
	 * to the file system.
	 *
	 * @param   array   $dataFocus  Focus point selected
	 * @param   string  $filePath   Path of the image
	 *
	 * @return void
	 *
	 * @since 4.0.0
	 */
	public function setFocus($dataFocus,$filePath);

	/**
	 * Public function for getting the focus point
	 * from the file system
	 *
	 * @param   string  $imgSrc  Path of the image
	 *
	 * @return string
	 *
	 * @since 4.0.0
	 */
	public function getFocus($imgSrc);

	/**
	 * Public functions for takeing giving the full
	 * source of the file.
	 *
	 * @return string
	 *
	 * @since 4.0.0
	 */
	public function imageSrc();

}
