<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.smart-crop
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

/**
 * Media Manager Smart Crop Action
 *
 * @since  4.0.0
 */
class PlgMediaActionSmartCrop extends \Joomla\Component\Media\Administrator\Plugin\MediaActionPlugin
{
	/**
	 * Load the language file on instantiation (for Joomla! 3.X only)
	 *
	 * @var    boolean
	 * @since  3.3
	 */
	protected $autoloadLanguage = true;

	/**
	 * Load the javascript files of the plugin.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function loadJs()
	{
		parent::loadJs();

		/*
		* This Library is to be used on the client side
		*/

		// HTMLHelper::_('script', 'plg_media-action_smartcrop/responsifyjs/responsify.js', array('version' => 'auto', 'relative' => true));
	}

	/**
	 * Load the CSS files of the plugin.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function loadCss()
	{
		parent::loadCss();
	}

	/*
	* Defineing the variables
	*
	* @var array
	*/
	protected $data_focus = array(
		'data-focus-top' => '',
		'data-focus-left' => '',
		'data-focus-bottom' => '',
		'data-focus-right' => ''
	);
}
